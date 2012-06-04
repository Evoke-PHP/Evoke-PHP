<?php
namespace Evoke\View\Text;

use DomainException,
	Evoke\HTTP\RequestIface,
	Evoke\Model\Data\TranslationsIface,
	InvalidArgumentException,
	LogicException;

class Translator implements TranslatorIface
{
	/** @property $defaultLanguage
	 *  @string The default language to use for translations.
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

	/** @property $translations
	 *  @object The translations data.
	 */
	protected $translations;

	public function __construct(
		DataIface    $translations,
		RequestIface $request,
		/* String */ $defaultLanguage,
		/* String */ $translationsFilename,
		/* String */ $langKey = 'l')
	{
		if (!is_string($defaultLanguage))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires defaultLanguage as string');
		}

		$this->defaultLangauge = $defaultLanguage;
		$this->langKey         = $langKey;
		$this->language        = NULL;
		$this->request         = $request;
		$this->translations    = $translations;
	}
   
	/******************/
	/* Public Methods */
	/******************/

	public function get(Array $params = array())
	{
		if (!isset($params['Key_Match']))
		{
			return $this->translations;
		}

		$lang = $this->getLanguage();
		$translationMatches = array();
		
		foreach ($this->translations as $trKey => $trRecord)
		{
			if (preg_match($keyMatch, $trKey))
			{
				$translationMatches[$trKey] = $trRecord[$lang];
			}
		}

		return $translationMatches;		
	}
	
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
		return $this->translations->getLanguages();
	}

	/** Get the language HTTP Query for the end of a URL (e.g 'l=EN' or 'l=ES').
	 *  @param lang The language to use or left empty for the current language.
	 *  @returns @string The HTTP Query parameter for the language.
	 */
	public function getLanguageHTTPQuery($lang='')
	{
		if (empty($lang))
		{
			$lang = $this->translations->getLanguage();
		}

		return http_build_query(array($this->langKey => $lang));
	}
	
	/** Get the translation for the current language.
	 *  @param trKey @string The translation key.
	 *  @param page @string The page to get the translation for.
	 *  @return @string The translation for the translation key.
	 */
	public function tr($trKey)
	{
		$translation = $this->translations->get($trKey);

		return $translation[$this->getLanguage()];
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