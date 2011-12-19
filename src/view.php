<?php


abstract class View implements Iface_View
{ 
   protected $app; ///< App
   protected $tr;  ///< Translator

   protected $setup;

   public function __construct(Array $setup)
   {
      $this->setup = array_merge(array('App'           => NULL,
				       'Event_Manager' => NULL,
				       'Translator'    => NULL),
				 $setup);

      $this->setup['App']->needs(
	 array('Instance' => array(
		  'Event_Manager' => $this->setup['Event_Manager'],
		  'Translator'    => $this->setup['Translator'])));

      $this->app =& $this->setup['App'];
      $this->tr  =& $this->setup['Translator'];

      $this->setup['Event_Manager']->connect('View.Write',
					     array($this, 'write'));
   }
}

// EOF