<?php

require_once 'system/constants.php';
require_once 'system/directories.php';
require_once 'system/files.php';

/// Translator
class Translator
{
   private $setup;
   
   public function __construct($setup=array())
   {
      $this->setup = array_merge(
	 array('Default_Language'  => DEFAULT_LANGUAGE,
	       'Languages'         => NULL,
	       'Lang_Key'          => 'l',
	       'Session_Manager'   => NULL,
	       'Translations_File' => TRANSLATIONS_FILE,
	       'TR_Arr'            => array()),
	 $setup);

      if (!$this->setup['Session_Manager'] instanceof Session_Manager)
      {
	 throw new InvalidArgumentException(
	    __METHOD__ . ' needs Session_Manager');
      }
      
      // Get the language definitions which set up the local translationArr.
      require $this->setup['Translations_File'];
      $this->setup['TR_Arr'] = $translationArr;

      if (!isset($this->setup['Languages']))
      {
	 $this->setup['Languages'] = $this->getLanguages();
      }

      if (!empty($_GET) && isset($_GET[$this->setup['Lang_Key']]) &&
	  isset($this->setup['Languages'][$_GET[$this->setup['Lang_Key']]]))
      {
	 $this->setLanguage($_GET[$this->setup['Lang_Key']]);
      }
   }
   
   /******************/
   /* Public Methods */
   /******************/

   /// Return the current language in its short form (e.g EN or ES).
   public function getLanguage()
   {
      if ($this->setup['Session_Manager']->issetKey($this->setup['Lang_Key']))
      {
	 return $this->setup['Session_Manager']->get($this->setup['Lang_Key']);
      }
      else
      {
	 return $this->setLanguage();
      }
   }

   // Return a keyed array to the languages used.
   public function getLanguages()
   {
      $languageArr = array();
      $langs = array_keys($this->setup['TR_Arr']['Languages']);

      foreach ($langs as $lang)
      {
	 $languageArr[$lang] = $this->get($lang . '_FULL');
      }

      return $languageArr;
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

      return http_build_query(array($this->setup['Lang_Key'] => $lang));
   }
   
   /// \todo Check whether HTTP_ACCEPT_LANGUAGE with all unsupported languages
   /// resolves to the default language.
   public function setLanguage($setLang='')
   {
      if (!empty($setLang))
      {
	 $this->setup['Session_Manager']->set($this->setup['Lang_Key'], $setLang);
      }
      else
      {
	 if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
	 {
	    // Parse the Accept-Language according to:
	    //    http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
	    preg_match_all(
	       '/([a-z]{1,8})' .     // Primary Tag e.g en   ISO-639
	       '(-[a-z]{1,8})*\s*' . // Sub Tag(s)  e.g -us  ISO-3166
	       // Optional quality factor
	       '(;\s*q\s*=\s*((1(\.0{0,3}))|(0(\.[0-9]{0,3}))))?/i',
	       $_SERVER['HTTP_ACCEPT_LANGUAGE'],
	       $langParse);

	    $langs = $langParse[1];
	    $quals = $langParse[4];

	    $numLanguages = count($langs);
	    $langArr = array();

	    for ($num = 0; $num < $numLanguages; $num++)
	    {
	       $newLang = strtoupper($langs[$num]);
	       $newQual = isset($quals[$num]) ?
		  (empty($quals[$num]) ? 1.0 : floatval($quals[$num])) : 0.0;

	       // Choose whether to upgrade or set the quality factor for the
	       // primary language.
	       $langArr[$newLang] = (isset($langArr[$newLang])) ?
		  max($langArr[$newLang], $newQual) : $newQual;
	    }

	    // sort list based on value
	    arsort($langArr, SORT_NUMERIC);
	    $acceptedLanguages = array_keys($langArr);
	    $preferredLanguage = reset($acceptedLanguages);

	    $this->setup['Session_Manager']->set(
	       $this->setup['Lang_Key'], $preferredLanguage);
	 }
	 else
	 {
	    $this->setup['Session_Manager']->set(
	       $this->setup['Lang_Key'], $this->setup['Default_Language']);
	 }
      }

      return $this->setup['Session_Manager']->get($this->setup['Lang_Key']);
   }

   public function getPage($page = 'default')
   {
      $lang = $this->getLanguage();
      $errText = $this->setup['TR_Arr']['Languages'][$lang]['Error_Text'];
      $trPage = str_replace(WEB_ROOT . '/', '', $page);

      // Missing translations should display an error.
      foreach($this->setup['TR_Arr']['Translation_Keys'] as $key => $val)
      {
	 $missingTranslations[$key] = $errText;
      }

      // Get the translations from the default page if it exists and merge it
      // with the specific page - keeping the specific page values.
      $defaultTranslations =
	 (isset($this->setup['TR_Arr']['Translations'][$lang]) &&
	  isset($this->setup['TR_Arr']['Translations'][$lang]['default'])) ?
	 $this->setup['TR_Arr']['Translations'][$lang]['default'] : array();

      $specificTranslations =
	 (isset($this->setup['TR_Arr']['Translations'][$lang]) &&
	  isset($this->setup['TR_Arr']['Translations'][$lang][$trPage])) ?
	 $this->setup['TR_Arr']['Translations'][$lang][$trPage] : array();

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
      $trans = $this->setup['TR_Arr']['Translations'][$lang];
      $trPage = str_replace(WEB_ROOT . '/', '', $page);

      if ($trPage === 'default')
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
	       $this->setup['TR_Arr']['Languages'][$lang]['Error_Text'];
	 }
      }
      else
      {
	 // If there is a specific translation return it, otherwise if the
	 // default translation exists return that, otherwise return an error.
	 if (isset($trans[$trPage]) && isset($trans[$trPage][$trKey]))
	 {
	    return $trans[$trPage][$trKey];
	 }
	 elseif (isset($trans['default']) && isset($trans['default'][$trKey]))
	 {
	    return $trans['default'][$trKey];
	 }
	 else
	 {
	    return $trKey . ' ' .
	       $this->setup['TR_Arr']['Languages'][$lang]['Error_Text'];
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
      $trans = $this->setup['TR_Arr']['Translations'][$lang];
      $trPage = str_replace(WEB_ROOT . '/', '', $page);

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
      if (($trPage !== 'default') && isset($trans[$trPage]))
      {
	 foreach ($trans[$trPage] as $specKey => $specTrans)
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
      $trans = $this->setup['TR_Arr']['Translations'][$lang];
      $trPage = str_replace(WEB_ROOT . '/', '', $page);

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
      if ($trPage !== 'default')
      {
	 foreach ($trans[$trPage] as $trKey => $trVal)
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
}

// EOF