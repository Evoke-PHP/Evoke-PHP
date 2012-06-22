<?php
namespace Evoke\View\Text;

use DomainException,
	Evoke\HTTP\RequestIface,
	Evoke\Model\Data\TranslationsIface,
	InvalidArgumentException,
	LogicException;

/**
 * Translator
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Translator implements TranslatorIface
{
	/**
	 * The default language to use for translations.
	 * @var string
	 */
	protected $defaultLanguage;

	/**
	 * The key that is used in HTTP queries for the language parameter.
	 * @var string
	 */
	protected $langKey;

	/**
	 * The current language that the translator is set to.
	 * @var string
	 */
	private $language;
	
	/**
	 * The languages that the translator supports.
	 * @var string[]
	 */
	protected $languages;

	/**
	 * Request object.
	 * @var Evoke\HTTP\RequestIface
	 */
	protected $request;

	/**
	 * The translations data.
	 * @var Evoke\Model\Data\TranslationsIface
	 */
	protected $translations;

	/**
	 * Construct a translator.
	 *
	 * @param Evoke\Data\TranslationsIface The translations data.
	 * @param Evoke\HTTP\RequestIface      The request.
	 * @param string                       The default language.
	 * @param string                       The language key for the query.
	 */
	public function __construct(TranslationsIface $translations,
	                            RequestIface      $request,
	                            /* String */      $defaultLanguage,
	                            /* String */      $langKey = 'l')
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

	/**
	 * Get the view (of the data) to be written.
	 *
	 * @param mixed[] Parameters for retrieving the view.
	 *
	 * @return mixed[] The view data.
	 */	
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
	
	/**
	 * Return the current language in its short form (e.g EN or ES).
	 *
	 * @return string The current language.
	 */
	public function getLanguage()
	{
		return $this->translations->getLanguage();
	}

	/**
	 * Get the languages that the translator has translations for.
	 *
	 * @return string[] The languages that the translator has translations for.
	 */
	public function getLanguages()
	{
		return $this->translations->getLanguages();
	}

	/**
	 * Get the language HTTP Query for the end of a URL (e.g 'l=EN' or 'l=ES').
	 *
	 * @param string The language to use or left empty for the current language.
	 *
	 * @return string The HTTP Query parameter for the language.
	 */
	public function getLanguageHTTPQuery($lang='')
	{
		if (empty($lang))
		{
			$lang = $this->translations->getLanguage();
		}

		return http_build_query(array($this->langKey => $lang));
	}
	
	/**
	 * Get the translation for the current language.
	 *
	 * @param string The translation key.
	 * @param string The page to get the translation for.
	 *
	 * @return string The translation for the translation key.
	 */
	public function tr($trKey)
	{
		$translation = $this->translations->get($trKey);

		return $translation[$this->getLanguage()];
	}
}
// EOF