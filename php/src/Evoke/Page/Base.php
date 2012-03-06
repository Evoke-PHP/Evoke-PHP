<?php
namespace Evoke\Page;
/// The basic definition of a page.
abstract class Base
{
	/** @property $Factory
	 *  Factory \object for building other objects.
	 */
	protected $Factory;

	/** @property InstanceManager
	 *  InstanceManager \object for creating new objects.
	 */
	protected $InstanceManager;
   
	public function __construct(Array $setup)
	{
		$setup +=array('Factory'         => NULL,
		               'InstanceManager' => NULL);

		if (!$setup['Factory'] instanceof \Evoke\Core\Factory)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires Factory');
		}
      
		if (!$setup['InstanceManager'] instanceof
		    \Evoke\Core\Iface\InstanceManager)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires InstanceManager');
		}

		$this->Factory         = $setup['Factory'];
		$this->InstanceManager = $setup['InstanceManager'];
	}

	/********************/
	/* Abstract Methods */
	/********************/

	/// Load the page.
	abstract public function load();
}
// EOF