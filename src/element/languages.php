<?php

class Element_Languages extends Element
{
   protected $setup;
   
   public function __construct($setup=array())
   {
      $this->setup = array_merge(
	 array('Format'     => '_h24.png',
	       'Lang_Dir'   => '/images/languages/',
	       'Translator' => NULL),
	 $setup);

      if (!$this->setup['Translator'] instanceof Translator)
      {
	 throw new InvalidArgumentException(
	    __METHOD__ . ' needs Translator');
      }
      
      $currentLanguage = $this->setup['Translator']->getLanguage();
      $languages = $this->setup['Translator']->getLanguages();

      if (isset($languages[$currentLanguage]))
      {
	 unset($languages[$currentLanguage]);
      }

      $otherLanguages = array();

      foreach($languages as $lang => $langFull)
      {
	 $otherLanguages[] = array(
	    'a',
	    array('href' => '?' . $this->setup[
		     'Translator']->getLanguageHTTPQuery($lang)),
	    array('Children' => array(
		     array('img',
			   array('src' => $this->setup['Lang_Dir'] . $lang .
				 $this->setup['Format'],
				 'alt' => $langFull)))));
      }
      
      $languageMenuElems = array(
	 array(
	    'li',
	    array(
	       'class' => 'Level_0'),
	    array(
	       'Children' => array(
		  array(
		     'a',
		     array(
			'href' => '?' . $this->setup[
			   'Translator']->getLanguageHTTPQuery(
			      $currentLanguage)),
		     array(
			'Children' => array(
			   array(
			      'img',
			      array('src' => $this->setup['Lang_Dir'] .
				    $currentLanguage . $this->setup['Format'],
				    'alt' => $this->setup[
				       'Translator']->get(
					  $currentLanguage . '_FULL')))))),
		  array(
		     'ul',
		     array('class' => 'Level_1'),
		     array('Children' => $otherLanguages))))));
      
      
      parent::__construct(array('ul',
				array('id' => 'Language_Menu'),
				array('Children' => $languageMenuElems)));
   }
}

// EOF