<?php
namespace Evoke\Service\Provider;

use Evoke\Service\CacheIface,
	InvalidArgumentException,
	ReflectionClass,
	ReflectionException;

class InterfaceRouter implements InterfaceRouterIface
{
	/**
	 * Reflection Cache
	 * @var Evoke\Service\CacheIface
	 */
	protected $reflectionCache;
	
	/**
	 * Rules that the router uses to route.
	 * @var Evoke\Service\Provider\Rule\RuleIface
	 */
	protected $rules = array();
	
	/**
	 * Construct an Interface Router object.
	 *
	 * @param Evoke\Service\CacheIface Reflection Cache
	 */
	public function __construct(CacheIface $reflectionCache)
	{
		$this->reflectionCache = $reflectionCache;
	}
	
	/******************/
	/* Public Methods */
	/******************/
	
	/**
	 * Add a rule to the router.
	 *
	 * @param Evoke\Service\Provider\Rule\RuleIface Provider Rule.
	 */
	public function addRule(Rule\RuleIface $rule)
	{
		$this->rules[] = $rule;
	}

	/**
	 * Route the Interface to a concrete class.
	 *
	 * @param string The interface name to route.
	 *
	 * @return string|bool The classname (or false if no concrete class could be
	 *                     found).
	 */
	public function route($interfaceName)
	{
		if (!is_string($interfaceName))
		{
			throw new InvalidArgumentException(
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

	/**
	 * Whether the class can be built into a concrete object.
	 *
	 * @param string The classname to check.
	 *
	 * @return bool Whether the classname corresponds to a concrete class.
	 */
	protected function isInstantiable($classname)
	{
		if ($this->reflectionCache->exists($classname))
		{
			return true;
		}
		
		try
		{
			$reflectionClass = new ReflectionClass($classname);
			return $reflectionClass->isInstantiable();
		}
		catch (ReflectionException $e)
		{
			return false;
		}
	}
}
// EOF