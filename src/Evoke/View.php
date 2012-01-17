<?php
namespace Evoke;

abstract class View implements Iface\View
{
   protected $app;
   protected $em; ///< Event_Manager
   protected $setup;
   protected $tr; ///< Translator

   public function __construct(Array $setup)
   {
      $this->setup = array_merge(array('App'           => NULL,
				       'Event_Manager' => NULL,
				       'Translator'    => NULL),
				 $setup);

      if (!$this->setup['Event_Manager'] instanceof Event_Manager)
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires Event_Manager');
      }

      if (!$this->setup['Translator'] instanceof Translator)
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires Translator');
      }
								  
      $this->app = $this->setup['App'];
      $this->em =& $this->setup['Event_Manager'];
      $this->tr =& $this->setup['Translator'];

      $this->em->connect('View.Write', array($this, 'write'));
   }
}
// EOF