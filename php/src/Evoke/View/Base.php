<?php
namespace Evoke\View;

abstract class Base implements \Evoke\Core\Iface\View
{
   protected $app;
   protected $em; ///< Event_Manager
   protected $setup;
   protected $tr; ///< Translator

   public function __construct(Array $setup)
   {
      $this->setup = array_merge(array('App'          => NULL,
				       'EventManager' => NULL,
				       'Translator'   => NULL),
				 $setup);

      if (!$this->setup['EventManager'] instanceof \Evoke\Core\EventManager)
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires EventManager');
      }

      if (!$this->setup['Translator'] instanceof \Evoke\Core\Translator)
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires Translator');
      }
								  
      $this->app = $this->setup['App'];
      $this->em =& $this->setup['EventManager'];
      $this->tr =& $this->setup['Translator'];

      $this->em->connect('View.Write', array($this, 'write'));
   }
}
// EOF