<?php
namespace Evoke\Element\Admin;

class Header extends \Evoke\Element\Base
{
   protected $setup;
   
   public function __construct(Array $setup)
   {
      $this->setup = array_merge(array('App'        => NULL,
				       'Languages'  => NULL,
				       'Translator' => NULL),
				 $setup);

      $this->setup['App']->needs(
	 array('Instance' => array('Translator' => $this->setup['Translator']),
	       'Set'      => array('Languages' => $this->setup['Languages'])));

      $tr =& $this->setup['Translator'];
      
      parent::__construct(
	 array('div',
	       array('class' => 'Admin_Header'),
	       array('Children' => array(		     
			array(
			   'a',
			   array('class' => 'Admin_Home',
				 'href' => '/admin/index.php?' .
				 $tr->getLanguageHTTPQuery()),
			   array('Children' => array(
				    array(
				       'img',
				       array('src' => '/images/admin_home.png',
					     'alt' => 'Home')),
				    array('span',
					  array(),
					  array('Text' => $tr->get(
						   'Admin_Home')))))),
			$this->setup['App']->get(
			   'Element_Language',
			   array('App'        => $this->setup['App'],
				 'Languages'  => $this->setup['Languages'],
				 'Translator' => $tr))))));
   }
}
// EOF