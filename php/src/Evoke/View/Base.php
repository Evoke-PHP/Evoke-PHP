<?php
namespace Evoke\View;

abstract class Base implements \Evoke\Core\Iface\View
{
	protected $em; ///< Event_Manager
	protected $instanceManager;
	protected $setup;
	protected $tr; ///< Translator

	public function __construct(Array $setup)
	{
		$this->setup = array_merge(array('EventManager'    => NULL,
		                                 'InstanceManager' => NULL,
		                                 'Translator'      => NULL),
		                           $setup);

		if (!$this->setup['EventManager'] instanceof \Evoke\Core\EventManager)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires EventManager');
		}

		if (!$this->setup['InstanceManager'] instanceof
		    \Evoke\Core\InstanceManager)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires InstanceManager');
		}
      
		if (!$this->setup['Translator'] instanceof \Evoke\Core\Translator)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Translator');
		}
						  
		$this->em =& $this->setup['EventManager'];
		$this->instanceManager =& $this->setup['InstanceManager'];
		$this->tr =& $this->setup['Translator'];

		$this->em->connect('View.Write', array($this, 'write'));
	}
}
// EOF