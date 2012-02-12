<?php
namespace Evoke\View;

use \Evoke\Core\Iface;

abstract class Base implements \Evoke\Core\Iface\View
{
	protected $EventManager;
	protected $InstanceManager;
	protected $Translator;

	public function __construct(Array $setup)
	{
		$setup += array('EventManager'    => NULL,
		                'InstanceManager' => NULL,
		                'Translator'      => NULL);

		if (!$setup['EventManager'] instanceof Iface\EventManager)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires EventManager');
		}

		if (!$setup['InstanceManager'] instanceof Iface\InstanceManager)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires InstanceManager');
		}
      
		if (!$setup['Translator'] instanceof Iface\Translator)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Translator');
		}
						  
		$this->EventManager    = $setup['EventManager'];
		$this->InstanceManager = $setup['InstanceManager'];
		$this->Translator      = $setup['Translator'];

		$this->EventManager->connect('View.Write', array($this, 'write'));
	}
}
// EOF