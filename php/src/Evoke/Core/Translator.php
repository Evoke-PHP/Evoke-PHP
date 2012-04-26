<?php
namespace Evoke\Core;

use Evoke\Iface\Core as ICore;

class Translator implements ICore\Translator
{
	/** @property $defaultLanguage
	 *  The default language to use for translations.
	 */
	protected $defaultLanguage;

	/** @property $langKey
	 *  @string The key that is used in HTTP queries for the language parameter.
	 */
	protected $langKey;

	/** @property $language
	 *  @string The current language that the translator is set to.
	 */
	private $language;
	
	/** @property $languages
	 *  @array The languages that the translator supports.
	 */
	protected $languages;

	/** @property $request
	 *  @object Request
	 */
	protected $request;

	/** @property $sessionManager
	 *  @object SessionManager
	 */
	protected $sessionManager;

	/** @property $translationArr
	 *  @array The array of translations.
	 */
	protected $translations;

	/** @property $translationsFilename
	 *  @string The filename for the file that holds the translations.
	 */
	private $translationsFilename;

	public function __construct(
		ICore\HTTP\Request   $request,
		ICore\SessionManager $sessionManager,
		/* String */         $defaultLanguage,
		/* String */         $translationsFilename,
		/* String */         $langKey = 'l')
	{
		if (!is_string($defaultLanguage))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires defaultLanguage as string');
		}

		if (!is_string($translationsFilename))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires translationsFilename');
		}

		$this->defaultLangauge 		= $defaultLanguage;
		$this->langKey         		= $langKey;
		$this->language             = NULL;
		$this->request              = $request;
		$this->sessionManager  		= $sessionManager;
		$this->translationsFilename = $translationsFilename;

		// Update the translations and langauges.
		$this->update();
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/// Return the current language in its short form (e.g EN or ES).
	public function getLanguage()
	{
		if (!isset($this->language))
		{
			$this->setLanguage();
		}

		return $this->language;
	}

	/// The languages that the translator has translations for.
	public function getLanguages()
	{
		return $this->languages;
	}

	/** Get the language HTTP Query for the end of a URL (e.g 'l=EN' or 'l=ES').
	 *  @param lang The language to use or left empty for the current language.
	 *  \returns \string The HTTP Query parameter for the language.
	 */
	public function getLanguageHTTPQuery($lang='')
	{
		if (empty($lang))
		{
			$lang = $this->getLanguage();
		}

		return http_build_query(array($this->langKey => $lang));
	}
	
	/// Reset the language so that it can be recalculated from the input sources.
	public function resetLanguage()
	{
		$this->language = NULL;
	}
	
	/** Set the language that the translator uses to display text.
	 *
	 *  There are a number of sources that determine the language that should be
	 *  used by the translator:
	 *
	 *  -# The value passed to the function.
	 *  -# URI Query parameter e.g ?l=EN
	 *  -# Session containing the previously set language.
	 *  -# HTTP Request AcceptLanguage header.
	 *  -# The Default Language the Translator was constructed with.
	 *  -# The order of the languages that the Translator has translations for.
	 *
	 *  These sources have the priority as in the above list for setting the
	 *  language.  The highest priority source with a language that exists within
	 *  the translator will be set (and stored in the session for future use).
	 *
	 *  @param lang \string The language to set (defaults to NULL).  If no
	 *  language is passed then the above sources should be used to determine the
	 *  correct language.
	 */
	public function setLanguage($lang=NULL)
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
				throw new \DomainException(
					__METHOD__ . ' Language must be valid for the translator. ' .
					'Unknown language: ' . var_export($lang, true));
			}
			
			$this->language = $lang;
			$this->sessionManager->set($this->langKey, $lang);
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

		if ($this->sessionManager->issetKey($this->langKey))
		{
			$lang = $this->sessionManager->get($this->langKey);

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

	public function getPage($page = 'default')
	{
		$lang = $this->getLanguage();
		$errText = $this->translations['Languages'][$lang]['Error_Text'];

		// Missing translations should display an error.
		foreach($this->translations['Translation_Keys'] as $key => $val)
		{
			$missingTranslations[$key] = $errText;
		}

		// Get the translations from the default page if it exists and merge it
		// with the specific page - keeping the specific page values.
		$defaultTranslations =
			(isset($this->translations['Translations'][$lang]) &&
			 isset($this->translations['Translations'][$lang]['default'])) ?
			$this->translations['Translations'][$lang]['default'] : array();

		$specificTranslations =
			(isset($this->translations['Translations'][$lang]) &&
			 isset($this->translations['Translations'][$lang][$page])) ?
			$this->translations['Translations'][$lang][$page] : array();

		$translations = array_merge(
			$missingTranslations, $defaultTranslations, $specificTranslations);

		return $translations;
	}

	/** Get the translation for the current language.
	 *  @param trKey \string The translation key.
	 *  @param page \string The page to get the translation for.
	 *  \return \string The translation for the translation key.
	 */
	public function get($trKey, $page='default')
	{
		$lang = $this->getLanguage();
		$trans = $this->translations['Translations'][$lang];

		if ($page === 'default')
		{
			// If there is a default translation return it otherwise return an
			// error.
			if (isset($trans['default']) && isset($trans['default'][$trKey]))
			{
				return $trans['default'][$trKey];
			}
			else
			{
				return $trKey . ' ' .
					$this->translations['Languages'][$lang]['Error_Text'];
			}
		}
		else
		{
			// If there is a specific translation return it, otherwise if the
			// default translation exists return that, otherwise return an error.
			if (isset($trans[$page]) && isset($trans[$page][$trKey]))
			{
				return $trans[$page][$trKey];
			}
			elseif (isset($trans['default']) && isset($trans['default'][$trKey]))
			{
				return $trans['default'][$trKey];
			}
			else
			{
				return $trKey . ' ' .
					$this->translations['Languages'][$lang]['Error_Text'];
			}
		}
	}
   
	/** Get all translations in the current language that match the key and
	 *  have been set to a valid value.
	 *  @param trKey \string The regexp for the translation key.
	 *  @param page \string The page to get the translation for.
	 *  \return \array The translation matches for the translation key.
	 */
	public function getAll($trKey, $page='default')
	{
		$translationMatches = array();
		$lang = $this->getLanguage();
		$trans = $this->translations['Translations'][$lang];

		// Find all of the default translations.
		if (isset($trans['default']))
		{
			foreach ($trans['default'] as $defKey => $defTrans)
			{
				if (preg_match($trKey, $defKey))
				{
					$translationMatches[$defKey] = $defTrans;
				}
			}
		}

		// Override the defaults with any specific translations that have been
		// defined.
		if (($page !== 'default') && isset($trans[$page]))
		{
			foreach ($trans[$page] as $specKey => $specTrans)
			{
				if (preg_match($trKey, $specKey))
				{
					$translationMatches[$specKey] = $specTrans;
				}
			}
		}

		return $translationMatches;
	}
   
	/** Get an array of translations in the current language for the specified
	 *  match.
	 *  @param keyMatch The translation key regexp to match.
	 *  @param page \string The page to get the translation for.
	 *  \return \array The translations that match the key regexp.
	 */
	public function getTranslations($keyMatch, $page='default')
	{
		$lang = $this->getLanguage();
		$trans = $this->translations['Translations'][$lang];

		$defaultTranslationMatches = array();
		$specificTranslationMatches = array();
      
		foreach ($trans['default'] as $trKey => $trVal)
		{
			if (preg_match($keyMatch, $trKey))
			{
				$defaultTranslationMatches[$trKey] = $trVal;
			}
		}
      
		// If the page is non-default we take translations from the specific page
		// in preference to the default translations.
		if ($page !== 'default')
		{
			foreach ($trans[$page] as $trKey => $trVal)
			{
				if (preg_match($keyMatch, $trKey))
				{
					$specificTranslationMatches[$trKey] = $trVal;
				}
			}
		}

		return array_merge($defaultTranslationMatches,
		                   $specificTranslationMatches);
	}

	/// Update the translations and languages from the translations file.
	public function update()
	{
		// Get the language definitions which set up the local translationArr.
		require $this->translationsFilename;
		$this->translations = $translationArr;
		$this->languages = $this->translations['Languages'];
	}
	
   /*********************/
   /* Protected Methods */
   /*********************/

	protected function isValidLanguage($lang)
	{
		return isset($lang) && array_key_exists($lang, $this->languages);
	}
}
// EOF