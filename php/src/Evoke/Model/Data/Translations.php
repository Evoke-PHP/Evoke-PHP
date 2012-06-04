<?php
namespace Evoke\Model\Data;

use Evoke\HTTP\RequestIface,
	InvalidArgumentException;

class Translations extends Data
{
	/** @property $currentLanguage
	 *  @string The current language that the translations data is representing.
	 */
	protected $currentLanguage = NULL;

	/** @property $defaultLanguage
	 *  @string The default language to use for translations.
	 */
	protected $defaultLanguage;

	/** @property $languages
	 *  @array The languages that the translations cover.
	 */
	protected $languages;
	
	/** @property page
	 *  @string Page
	 */
	protected $page;

	/** @property $request
	 *  @object Request
	 */
	protected $request;
	
	/** Construct a Translations object that modifies the iteration over the
	 *  data to be page aware.
	 *  @param page @string Page.
	 *  @param data @array  Data.
	 */
	public function __construct(RequestIface $request,
	                            /* String */ $page,
	                            Array        $data = array())
	{
		if (!is_string($page))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires page as string');
		}

		// Constructing the parent sets the data providing the inital conditions
		// for the data and languages properties.
		parent::__construct($data);

		$this->page = $page;
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Get the current language that the translator is translating to.
	 *  @return @string The current language.
	 */
	public function getLanguage()
	{

	}

	/** Get all of the languages that the translations are provided in.
	 *  @return @array The languages that we have translations for.
	 */
	public function getLanguages()
	{

	}
	
	/** Set the data for the translations, using the page specific value if it
	 *  is set.
	 *  @param data @array The translations.
	 */
	public function setData(Array $data)
	{
		$translations = array();
		
		foreach ($data as $record)
		{
			if (empty($record['Page']))
			{
				if (!isset($translations[$record['Name']]))
				{
					$translations[$record['Name']] = $record;
				}
			}
			elseif ($record['Page'] === $this->page)
			{
				$translations[$record['Name']] = $record;
			}
		}

		$this->data = $translations;
		$this->rewind();

		// Calculate the languages within the translations.
		$record = first($this->data);
		unset($record['ID']);
		unset($record['Name']);
		$this->languages = array_keys($record);
	}

	/** Set the language that the translations are retrieved in.
	 *
	 *  There are a number of sources that determine the language that should be
	 *  used for the translations:
	 *
	 *  -# The value passed to this method.
	 *  -# URI Query parameter e.g ?l=EN
	 *  -# HTTP Request AcceptLanguage header.
	 *  -# The Default Language the Translator was constructed with.
	 *  -# The order of the languages as they appear in the data.
	 *
	 *  These sources have the priority as in the above list for setting the
	 *  language.  The highest priority source with a language that exists within
	 *  the translator will be set.
	 *
	 *  @param lang @string The language to set (defaults to NULL).  If no
	 *  language is passed then the above sources should be used to determine the
	 *  correct language.
	 */
	public function setLanguage($lang = NULL)
	{
		if (empty($this->languages))
		{
			throw new LogicException(
				__METHOD__ . ' no languages exist in the translator to allow ' .
				'setting of the language.');
		}
		
		if (isset($lang))
		{
			if (!$this->isValidLanguage($lang))
			{
				throw new DomainException(
					__METHOD__ . ' Language must be valid for the translator. ' .
					'Unknown language: ' . var_export($lang, true));
			}
			
			$this->language = $lang;
			return;
		}

		if ($this->request->issetQueryParam($this->langKey))
		{
			$lang = $this->request->getQueryParam($this->langKey);

			if ($this->isValidLanguage($lang))
			{
				$this->language = $lang;
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

			if ($this->isValidLanguage($l))
			{
				$this->language = $l;
				return;
			}
		}
			
		if ($this->isValidLanguage($this->defaultLanguage))
		{
			$this->language = $this->defaultLanguage;
			return;
		}

		$this->language = reset($this->languages);
	}
}
// EOF
