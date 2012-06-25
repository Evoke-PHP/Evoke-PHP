<?php
namespace Evoke\Service;

use Evoke\Service\CacheIface,
	InvalidArgumentException,
	ReflectionClass,
	ReflectionParameter;

/**
 * Dependency Injection Provider
 *
 * The dependency injection provider serves two purposes:
 *
 * - __Lazy Injection__
 * - __Object creation__
 *
 * ## History
 *
 * This Provider class like most of Evoke has been modified numerous times.  The
 * ideas and core of the code are from Daniel Lowrey's Artax provider class,
 * with some changes to bring it into the style of Evoke.  Artax is an
 * event-driven Application engine.  It can be found here:
 *
 *     https://github.com/rdlowrey/Artax
 *
 * This file originated from copying and editing the Provider of the Atrax-Core
 * dev branch: 648624a3cb.
 *
 * My thanks go to Daniel Lowrey for the creation of the Artax provider and his
 * permission for me to use the code from the Artax Provider class.  The
 * provider was an awesome idea that was done well in Artax.  I recommend Artax,
 * it is very high quality code.
 *
 * ## Rationale
 *
 * #### Lazy Injection
 *
 * Building an object graph can be a lot of work.  Each object in the object
 * graph requires its construction parameters to be supplied.
 *
 * - Scalar/array configuration values.
 * - Typehinted Objects and interfaces.
 * - Possibly overriden default values.
 *
 * Ideally we would like to do as little work as possible.  The provider class
 * can remove some of our burden by:
 *
 * - Creating concrete objects that have no unknown dependencies.
 * - Override particular default values.
 *
 * It cannot provide the Scalar/Array configuration values.  I also choose not
 * to let it route interfaces to concrete classes (I did this before but I am
 * choosing not to now to avoid the global state that it brought).
 *
 * #### Object Creation
 *
 * Creating an object using `new` on a particular class couples the code tightly
 * to the class it creates.  By having an object that creates others we have a
 * loose binding to an object that can create others.  It also provides us with
 * the seam that isolates each unit for unit testing.
 *
 * @author Daniel Lowrey (See History)
 * @link https://github.com/rdlowrey/Artax
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) Daniel Lowrey, Paul Young
 * @license MIT
 * @package Service
 */
class Provider implements ProviderIface
{
	/**
	 * Reflection Cache
	 * @var Evoke\Service\CacheIface
	 */
	protected $reflectionCache;

	/**
	 * Construct a Provider object.
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
	 * Make an object and return it.
	 *
	 * This is the way to create objects using Evoke.  Using this method
	 * decouples object creation from your code.  This makes it easy to test
	 * your code as it is not tightly bound to the objects that it depends on.
	 *
	 * @param string  Classname, including namespace.
	 * @param mixed[] Construction parameters.  Only the parameters that cannot
	 *                be lazy loaded need to be passed.  They are:
	 *
	 * - Scalars / Arrays that have no default
	 * - Objects that satisfy the interface typehints
	 * - Non-trivial objects (those that have a dependency as above).
	 *
	 * @return mixed The object that has been created.
	 */
	public function make($classname, Array $params=array())
	{
		$passedParameters = $this->convertPascalToCamel($params);
		$reflection = $this->getReflection($classname);
		
        // If there is no possibility of passing parameters to the code then
        // just instantiate the object and return it.
		if (!isset($reflection['Params']))
        {            
	        return new $classname;
        }

        // Calculate the dependencies for the object.
	    $deps = array();
        
	    foreach ($reflection['Params'] as $reflectionParam)
        {
	        $deps[] = $this->getDependency($reflectionParam, $passedParameters);
        }

	    // Create the object.
	    $object = $reflection['Class']->newInstanceArgs($deps);
	    
        return $object;
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	/**
	 * Get the dependency for the currently refleced parameter.
	 *
	 * @param ReflectionParameter The currently reflected parameter.
	 * @param mixed[]             Hard-coded values supplied by the user when
	 *                            calling make.
	 *
	 * @return mixed An injected dependency.
	 */
	protected function getDependency(ReflectionParameter $reflectionParam,
	                                 Array               $passedParameters)
	{
		if (isset($passedParameters[$reflectionParam->name]))
		{
			// Use what the user passed us.
			return $passedParameters[$reflectionParam->name];
		}

		if ($reflectionParam->isDefaultValueAvailable())
		{
			// Use a default value.
			return $reflectionParam->getDefaultValue();
		}

		$depClass = $reflectionParam->getClass();

		if (!isset($depClass))
		{
			$message = 'Missing ';
			$message .= $reflectionParam->isArray() ? 'Array' : 'Scalar';
			$message .= ' dependency.';
			
			// It must be an unset Scalar or Array.  Bail hard and early.
			throw new InvalidArgumentException($message);
		}

		if ($depClass->isInstantiable())
		{
			// Make an instantiable object.
			return $this->make($depClass->name);
		}

		if ($reflectionParam->isOptional())
		{
			return null;
		}
		
		throw new InvalidArgumentException('Missing Interface Dependency');
	}
	
	/**
	 * Get the reflection for the class.
	 *
	 * @param string The full classname (including the namespace).
	 *
	 * @return mixed[] Array of format:
	 *
	 * <pre><code>
	 * array('Class' =>  $reflectionClass,
	 *       'Params' => $reflectionParams);
	 * </code></pre>
	 */
	protected function getReflection($classname)
	{
		// Get the reflection using the cache if possible.
		if ($this->reflectionCache->exists($classname))
		{
			return $this->reflectionCache->get($classname);
		}

		// Build the reflection.
		$reflectionClass = new ReflectionClass($classname);
		$constructor = $reflectionClass->getConstructor();
		$reflectionParams = isset($constructor) ?
			$constructor->getParameters() : NULL;
		$reflection = array('Class'  => $reflectionClass,
		                    'Params' => $reflectionParams);
		
		$this->reflectionCache->set($classname, $reflection);			

		return $reflection;
	}
	
	/*******************/
	/* Private Methods */
	/*******************/

	/**
	 * Convert an array with keys in pascal to the same array, but with the
	 * keys in camelCase.
	 *
	 * @param mixed[] The array to convert.
	 */
	private function convertPascalToCamel(Array $pascalArr)
	{
		$camelArr = array();
		
		foreach ($pascalArr as $key => $val)
		{
			$camelArr[lcfirst(implode('', explode('_', $key)))] = $val;
		}

		return $camelArr;
	}
}
// EOF
