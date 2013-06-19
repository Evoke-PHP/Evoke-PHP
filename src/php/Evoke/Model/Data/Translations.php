<?php
/**
 * Translations Data
 *
 * @package Model\Data
 */
namespace Evoke\Model\Data;

use DomainException,
	Evoke\HTTP\RequestIface,
	InvalidArgumentException,
	RuntimeException;

/**
 * Translations Data
 *
 * Model the translations data, respecting the languages that we are requested
 * to use, and the languages that we have translations for.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Model\Data
 */
class Translations extends DataAbstract implements TranslationsIface
{
	/**
	 * The translation key to use when no translation is found.
	 * @var string
	 */
	const NO_TRANSLATION_FOUND = 'NO_TRANSLATION_FOUND';

	/**
	 * The current language that the translations data is representing.
	 * @var string 
	 */
	protected $currentLanguage;

	/**
	 * The currentPage that the translations are for.
	 * @var string
	 */
	protected $currentPage;

	/**
	 * The default language to use for translations.
	 * @var string 
	 */
	protected $defaultLanguage;

	/**
	 * The language key used to define the desired language in GET requests.
	 * @var string
	 */
	protected $langKey;
	
	/**
	 * The field that identifies the translation.
	 * @var string
	 */
	protected $nameField;

	/**
	 * The fields in the translations that are not language translations.
	 * (Note: The Name field does not need to be specified in this array).
	 * @var array
	 */
	protected $nonLanguageFields;	
	
	/**
	 * The field that identifies the page that the translation is for.
	 * @var string
	 */
	protected $pageField;
	
	/**
	 * The Request object for determining the Accept-Language.
	 * @var Request
	 */
	protected $request;

	/**
	 * The raw translations before any filtering by page and language.
	 * @var Array[]
	 */
	protected $translations;
	
	/**
	 * Construct a Translations object that modifies the iteration over the
	 * data to be page aware.
	 *
	 * @param RequestIface The request object.
	 * @param Array[]  	   Raw translations data.
	 * @param string   	   The currentPage that we want the translations for.
	 * @param string   	   The language query key used in GET requests to
	 *                     specify the desired language of translations.
	 * @param string   	   The name field for the translations.
	 * @param string[] 	   The fields in the data that don't represent
	 *                     translations in a language.
	 * @param string   	   The page field for the translations.
	 */
	public function __construct(RequestIface $request,
	                            Array        $translations,
	                            /* String */ $currentPage       = '',
	                            /* String */ $defaultLanguage   = 'EN',
	                            /* String */ $langKey           = 'l',
	                            /* String */ $nameField         = 'Name',
	                            Array        $nonLanguageFields = array('ID'),
	                            /* String */ $pageField         = 'Page')
	{
		if (!is_string($currentPage))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires currentPage as string');
		}

		$this->currentPage       = $currentPage;
		$this->defaultLanguage   = $defaultLanguage;
		$this->langKey           = $langKey;
		$this->nameField         = $nameField;
		$this->nonLanguageFields = $nonLanguageFields;
		$this->pageField         = $pageField;
		$this->request           = $request;
		$this->translations      = $translations;

		// Constructing the parent sets the data providing the inital conditions
		// for the data and languages properties.
		parent::__construct($translations);
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the current language that the translator is translating to.
	 *
	 * @return string The current language.
	 */
	public function getLanguage()
	{
		// If the current language has not already been set then the current
		// value is obtained with an empty call to the set language method so
		// that the correct language priorities can be analyzed to determine
		// the current language.
		if (!isset($this->currentLanguage))
		{
			$this->setLanguage();
		}

		return $this->currentLanguage;
	}
	
	/**
	 * Get all of the languages that the translations are provided in for the
	 * current record.
	 *
	 * @return string[] The languages that we have translations for.
	 */
	public function getLanguages()
	{
		$currentRecord = current($this->data);
		
		if ($currentRecord === false)
		{
			return array();
		}

		unset($currentRecord[$this->nameField]);
		unset($currentRecord[$this->pageField]);
		
		return array_keys(
			array_diff_key($currentRecord,
			               array_flip($this->nonLanguageFields)));
	}

	/**
	 * Get the key that is used for the language query parameter.
	 *
	 * @return string The key used in query parameters to set the language.
	 */
	public function getLanguageKey()
	{
		return $this->langKey;
	}

	/**
	 * Return whether the language is modelled by the translations.
	 *
	 * @param string The language to check.
	 * @return bool Whether the language is modelled by the translations.
	 */
	public function hasLanguage($language)
	{
		$currentRecord = current($this->data);

		return isset($currentRecord[$language]);		
	}

	/**
	 * Reset the language for the translations so that another language can be
	 * chosen.
	 */
	public function resetLanguage()
	{
		$this->currentLanguage = NULL;
	}
	 
	
	/**
	 * Set the data for the translations, using the page specific value if it
	 * is set.
	 *
	 * @param Array[] The raw data for the translations.
	 */
	public function setData(Array $data)
	{
		$this->translations = $data;
		$this->data = $this->filterTranslations();
		$this->rewind();
	}

	/**
	 * Set the language that the translations are retrieved in.
	 *
	 * There are a number of sources that determine the language that should be
	 * used for the translations:
	 *
	 * - The value passed to this method.
	 * - URI Query parameter e.g ?l=EN
	 * - HTTP Request AcceptLanguage header.
	 * - The Default Language the Translator was constructed with.
	 * - The order of the languages as they appear in the first translation.
	 *
	 * These sources have the priority as in the above list for setting the
	 * language.  The highest priority source with a language that exists within
	 * the first translation will be set.
	 *
	 * @param string lang The language to set.  If no language is passed then
	 *                    the above sources should be used to determine the
	 *                    correct language.
	 */
	public function setLanguage($lang = NULL)
	{
		if (isset($lang))
		{
			if (!$this->hasLanguage($lang))
			{
				throw new DomainException(
					__METHOD__ . ' Language must be valid for the ' .
					'translator. Unknown language: ' . var_export($lang, true));
			}
			
			$this->currentLanguage = $lang;
			return;
		}

		if ($this->request->issetQueryParam($this->langKey))
		{
			$lang = $this->request->getQueryParam($this->langKey);

			if ($this->hasLanguage($lang))
			{
				$this->currentLanguage = $lang;
				return;
			}
		}

		$acceptLanguages = $this->request->parseAcceptLanguage();
		
		foreach ($acceptLanguages as $lang)
		{
			// The accept langauges are ordered by preference, so the first one
			// that is available should be the one that we choose.  Accept
			// languages can be specific e.g en-us, but the translator is not so
			// specific.  So we only need the major part of the language.
			$l = strtoupper(
				preg_replace('/^([[:alpha:]]+)-?.*/', '\1', $lang['Language']));

			if ($this->hasLanguage($l))
			{
				$this->currentLanguage = $l;
				return;
			}
		}
			
		if ($this->hasLanguage($this->defaultLanguage))
		{
			$this->currentLanguage = $this->defaultLanguage;
			return;
		}

		$currentLanguages = $this->getLanguages();
		$this->currentLanguage = reset($currentLanguages);
	}

	/**
	 * Get the translation data for a single entry.
	 *
	 * @param string The key for the translation to retrieve.
	 *
	 * @return string The translated string.
	 */
	public function tr(/* String */ $key)
	{
		if (!isset($this->currentLanguage))
		{
			$this->setLanguage();
		}

		if (isset($this->data[$key][$this->currentLanguage]))
		{
			return $this->data[$key][$this->currentLanguage];
		}

		if (!isset($this->data[$key]))
		{
			if ($key !== self::NO_TRANSLATION_FOUND)
			{
				trigger_error(
					'No translation for: ' . $key .
					(!empty($this->currentPage) ? '' :
					 ' on page: ' . $this->currentPage),
					E_USER_WARNING);
				
				return $this->tr(self::NO_TRANSLATION_FOUND) . $key;
			}
			else
			{
				throw new RuntimeException(
					self::NO_TRANSLATION_FOUND . ' must be defined for ' .
					'undefined translations.');
			}
		}

		throw new DomainException(
			' translation is not in ' . $this->currentLanguage . ' for: ' .
			$key . ' on page: ' . $this->currentPage);
	}
	
	/**
	 * Get the translation data for a single entry in the specified language.
	 *
	 * @param string The key for the translation to retrieve.
	 * @param string The language that we want the translation in.
	 *
	 * @return string The translated string.
	 */
	public function trSpecific($key, $language)
	{
		if (isset($this->data[$key][$this->currentLanguage]))
		{
			return $this->data[$key][$this->currentLanguage];
		}

		if (!isset($this->data[$key]))
		{
			if ($key !== self::NO_TRANSLATION_FOUND)
			{
				trigger_error(
					'No translation for: ' . $key .
					(!empty($this->currentPage) ? '' :
					 ' on page: ' . $this->currentPage),
					E_USER_WARNING);
				
				return $this->trSpecific(
					self::NO_TRANSLATION_FOUND, $language) . $key;
			}

			throw new RuntimeException(
				'Either all translations must be defined or ' .
				self::NO_TRANSLATION_FOUND . ' must be defined for missing ' .
				'translations.');
		}

		throw new DomainException(
			' translation is not in ' . $language . ' for: ' .
			$key . ' on page: ' . $this->currentPage);
	}
	
	/*********************/
	/* Protected Methods */
	/*********************/

	/**
	 * Filter the translations using the correct page.
	 *
	 * @returns Array[] The translations for the current page.
	 */
	protected function filterTranslations()
	{
		$filteredData = array();
		
		foreach ($this->translations as $record)
		{
			if (empty($record[$this->pageField]))
			{
				if (!isset($filteredData[$record[$this->nameField]]))
				{
					$filteredData[$record[$this->nameField]] = $record;
				}
			}
			elseif ($record[$this->pageField] === $this->currentPage)
			{
				$filteredData[$record[$this->nameField]] = $record;
			}
		}

		return $filteredData;

	}
}
// EOF
