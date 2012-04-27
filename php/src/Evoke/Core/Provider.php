<?php
namespace Evoke\Core;

use Evoke\Iface\Core as ICore;

/** A dependency injection/provider class (Thanks to rdlowrey see comments).
 *
 *  #### History ####
 *
 *  The Evoke Provider class is a combination of Evoke's old InstanceManager
 *  class with Daniel Lowrey's Artax Provider Class.  Code and ideas were used
 *  with permission from Daniel Lowrey.  Artax is an event-driven Application
 *  engine.  It can be found here: https://github.com/rdlowrey/Artax-Core
 * 
 *  This file takes from the Provider of the Atrax-Core dev branch: 648624a3cb.
 *  @author Daniel Lowrey <rdlowrey@gmail.com>
 *
 *  Modifications made by Paul Young.  The heart of the Provider logic remains
 *  unchanged from the awesome implementation of rdlowrey.  There have been
 *  widespread changes to bring the class into the style of Evoke.  A simplified
 *  interface was also chosen.
 *
 *  #### Rationale ####
 *
 *  The provider is responsible for creating objects and shared services.  It is
 *  used to create objects and retrieve shared objects in the system.  Using the
 *  provider decouples code that would otherwise use the new operator and gives
 *  control for the creation of instances, avoiding nasty singleton type
 *  methods.
 *
 *  Calling `new foo()` or bar::getInstance in code makes that code require a
 *  foo class or a bar class with a getInstance method.  This is a tight
 *  coupling that can be avoided by using this class.  By using this class the
 *  only requirement created in code that creates or gets instances of objects
 *  is that it is always injected with an object that implements the Provider
 *  interface.
 *
 *  Using this class for all of your objects and shared resources makes it easy
 *  to test your code (you won't need stubs) as you will be able to inject all
 *  of the required testing objects at test time.
 *
 *  #### Usage Scenarios ####
 *
 *  ## Object with Concrete Typehinted Dependencies ##
 *
 *  @code
 *  class Concrete
 *  {
 *      // Cement, Water, Sand and Gravel are real (concrete) objects.
 *      public function __construct(Cement $cement,
 *                                  Water  $water,
 *                                  Sand   $sand,
 *                                  Gravel $greyGravelForMixing) {}
 *  }
 *
 *  // Automatic creation of Concrete Typehinted Dependencies!
 *  $concrete = $provider->make('Concrete');
 *
 *  class DistilledWater extends Water {}
 *
 *  // Specialization combined with automatic injection.
 *  $specialWater = $provider->make('DistilledWater');
 *  $specialGravel = $provider->make('Gravel');
 *  $specialConcrete = $provider->make(
 *      'Concrete',
 *      array('Grey_Gravel_For_Mixing' => $specialGravel,
 *            'Water'                  => $specialWater)); 
 *  @endcode
 *
 *  Observe how we pass in dependencies using the second argument to make.  The
 *  Pascal_Case from the array is converted to camelCase to match the name of
 *  the constructor argument (Grey_Gravel_For_Mixing => greyGravelForMixing).
 *  This matches the Evoke standard of using Pascal_Case for array indexes and
 *  camelCase for variables.
 *
 *  ## Object with Scalars ##
 *
 *  @code
 *  namespace Weight;
 *
 *  class Scale
 *  {
 *      /// weightLimit is an integer.
 *      public function __construct($weightLimit) {}
 *  }
 *
 *  // Injection of a scalar value! (This also works in combination with other
 *  // dependencies).
 *  $provider->make('\Weight\Scale', array('Weight_Limit' => 50));
 *  @endcode
 *
 *  ## Object with Interfaces ##
 *
 *  @code
 *  // Injection of interfaces!?! (Using the Interface Router!)  An interface
 *  // router must be passed to the Provider.  This router is responsible for
 *  // connecting interfaces with concrete classes.  so that the following is
 *  // possible (given an InterfaceRouter with valid rules for the classes).
 *  class UI
 *  {
 *      public function __construct(\Evoke\Iface\User        $user,
 *                                  \Evoke\Iface\Core\Writer $writer) {}
 *  }
 *
 *  $provider->make(
 *      'UI', array('Writer' => $provider->make('\Evoke\Core\Writer\XHTML')));
 *  @endcode
 *
 *  We can even inject interfaces!?!  We have a default conversion that renames
 *  the interface by replacing \Iface\ with \.  So, \Evoke\User is automatically
 *  injected.  However the writer interface points to an abstract or otherwise
 *  undesirable class which we pass in manually.
 *
 */
class Provider implements ICore\Provider
{
	/** @property $interfaceRouter
	 *  @object interfaceRouter
	 */
	protected $interfaceRouter;
	
	/** @property $reflections
	 *  @array The cached class and parameter reflections for classes.
	 */
	protected static $reflections = array();

	/** @property $shared
	 *  @array The store of shared classes (grouped by class and parameters).
	 *  If the same request to make an object of the class is received with the
	 *  same parameters then the stored shared class shall be returned.
	 */
	protected static $shared = array();

	/** Construct a Provider object.
	 *  @param $interfaceRouter @object InterfaceRouter
	 */
	public function __construct(ICore\Provider\Iface\Router $interfaceRouter)
	{
		$this->interfaceRouter = $interfaceRouter;
	}

	
	/******************/
	/* Public Methods */
	/******************/

	/** Make an object and return it.
	 *
	 *  This is the way to create objects (or retrieve shared services) using
	 *  Evoke.  Using this method decouples object creation from your code.
	 *  This makes it easy to test your code as it is not tightly bound to the
	 *  objects that it depends on.
	 *
	 *  @param className @string Classname, including namespace.
	 *
	 *  @param params    @array  Construction parameters.  Only the parameters
	 *  that cannot be lazy loaded (scalars with no default or interfaces that
	 *  have no corresponding concrete object with the mapped classname) need to
	 *  be passed.
	 *
	 *  @return The object that has been created.
	 */
	public function make($className, Array $params=array())
	{
		$passedParameters = $this->pascalToCamel($params);

		// Is this class a shared service.
		if (isset(self::$shared[$className]))
		{
			foreach (self::$shared[$className] as $sharedEntry)
			{
				if ($sharedEntry['Params'] === $params)
				{
					return $sharedEntry['Object'];
				}
			}
		}
		
		// Reflect the class if we haven't already done so.
		if (!isset(self::$reflections[$className]))
        {
	        $this->reflect($className);
        }

        // If there is no possibility of passing parameters to the code then
        // just instantiate the object and return it.
		if (!isset(self::$reflections[$className]['Params']))
        {            
	        return new $className;
        }

        // Use the passed parameters, falling back on the reflected parameters
		// for automatic lazy injection to create all of the dependencies.
	    $deps = array();
        
	    foreach (self::$reflections[$className]['Params'] as $reflectionParam)
        {
	        $deps[] = $this->getDependency($reflectionParam, $passedParameters);
        }

	    $obj = self::$reflections[$className]['Class']->newInstanceArgs($deps);
	    
	    // If this is a shared class then we are creating it for the first time.
	    if (isset(self::$shared[$className]))
	    {
		    self::$shared[$className][] = array('Object' => $obj,
		                                        'Params' => $passedParameters);
	    }
	    
        return $obj;
	}

	/** Set the specified class to be shared by the Provider.  The make method
	 *  will return a shared object for this class while the class remains
	 *  shared.
	 *  @param className @string  Classname (including namespace).
	 */
	public function share($className)
	{
		if (!isset(self::$shared[$className]))
		{
			self::$shared[$className] = array();
		}
	}

	/** Stop the class from being shared by the Provider, forcing a new object
	 *  to be created for the class each time it is made using make.
	 *  @param className @string The classname to unshare.
	 */
	public function unshare($className)
	{
		unset(self::$shared[$className]);
	}
	
	/*********************/
	/* Protected Methods */
	/*********************/

	/** Get the dependency for the currently refleced parameter.
	 *  @param reflectedParam   @object The currently reflected parameter.
	 *  @param passedParameters @array  Hard-coded values supplied by the user
	 *                                  when calling make.
	 *  @return @mixed An injected dependency.
	 */
	protected function getDependency(\ReflectionParameter $reflectionParam,
	                                 Array                $passedParameters)
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
			// Use NULL for something that we cannot reflect upon to get the
			// required details to do an automatic injection.
			return NULL;
		}

		if ($depClass->isInstantiable())
		{
			// Make an instantiable object.
			return $this->make($depClass->name);
		}

		// Should we try converting it from an interface to a concrete?
		if ($depClass->isInterface())
		{
			$concreteClass =
				$this->interfaceRouter->route($depClass->name);

			if ($concreteClass != false)
			{
				// Make the concrete class that we were routed to.
				return $this->make($concreteClass);
			}
		}

		/** \todo Investigate, should an exception be thrown here?  It is
		 *  likely that a required scalar parameter was not passed or an
		 *  interface without a route to a concrete class was encountered.
		 */		
		// Use NULL, why not?  What could possibly go wrong?  I'd be expecting
		// to catch exceptions from the constructor when we reach here.
		return NULL;
	}
	
	/** Reflect the class, storing it in the reflections array.
	 *  @param className @string The full classname (including the namespace).
	 */
	protected function reflect($className)
	{
		$reflectionClass = new \ReflectionClass($className);
		$constructor = $reflectionClass->getConstructor();
		$reflectionParams =
			(isset($constructor) ? $constructor->getParameters() : NULL);
		
		self::$reflections[$className] = array('Class'  => $reflectionClass,
		                                       'Params' => $reflectionParams);		
	}
	
	/*******************/
	/* Private Methods */
	/*******************/

	/** Convert an array with keys in pascal to the same array, but with the
	 *  keys in camelCase.
	 *  @param pascalArr @array The array to convert.
	 */
	private function pascalToCamel(Array $pascalArr)
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