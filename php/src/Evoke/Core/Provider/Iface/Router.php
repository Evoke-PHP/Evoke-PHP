<?php
namespace Evoke\Core\Provider\Iface;

use Evoke\Iface\Core as ICore;

class Router implements ICore\Provider\Iface\Router
{
	/** @property $rules
	 *  @array of rules that the router uses to route.
	 */
	protected $rules = array();

	/** Add a rule to the router.
	 *  @param rule @object HTTP URI Rule object.
	 */
	public function addRule(ICore\Provider\Iface\Rule $rule)
	{
		$this->rules[] = $rule;
	}

	/** Route the Interface to a concrete class.
	 *  @param interfaceName @string The interface name to route.
	 *  @return @mixed The classname (or false if no concrete class could be found).
	 */
	public function route($interfaceName)
	{
		if (!is_string($interfaceName))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires interfaceName as string');
		}
      
		foreach ($this->rules as $rule)
		{
			if ($rule->isMatch($interfaceName))
			{
				$classname = $rule->getClassname($interfaceName);

				if ($this->isInstantiable($classname))
				{
					// Return the concrete class that has been found.
					return $classname;
				}
			}
		}

		// No concrete classes were found for the interface.
		return false;
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	/** Whether the class can be built into a concrete object.
	 *  @param classname @string The classname to check.
	 *  @return @bool Whether the classname corresponds to a concrete class.
	 */
	protected function isInstantiable($classname)
	{
		try
		{
			$reflectionClass = new ReflectionClass($classname);
			return $reflectionClass->isInstantiable();
		}
		catch (\ReflectionException $e)
		{
			return false;
		}
	}
}
// EOF