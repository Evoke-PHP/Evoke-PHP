<?php
namespace Evoke\Core;

/** A dependency injection/provider class (Thanks to rdlowrey see comments).
 *
 *  The Evoke Provider class, derived from the Artax Provider Class File with
 *  permission from Daniel Lowrey.  Artax is an event-driven Application engine.
 *  It can be found here: https://github.com/rdlowrey/Artax-Core
 * 
 *  Note: This file was forked from the Atrax-Core dev branch: 648624a3cb.
 * 
 *  @author     Daniel Lowrey <rdlowrey@gmail.com>
 *
 *  Modifications made by Paul Young.  The heart of the Provider logic remains
 *  unchanged from the awesome implementation of rdlowrey.  There have been
 *  widespread changes to bring the class into the style of Evoke.  A simplified
 *  interface was also chosen.
 *
 *  The Provider is a dependency injection container for the lazy instantiation
 *  of objects.  The build and get methods `Provider::make` automatically instantiates an instance of the
 *  given class name using reflection to determine the class's constructor
 *  parameters. Non-concrete dependencies
 *  may also be correctly instantiated using custom injection definitions.
 * 
 *  The `Provider::share` method can be used to "recycle" an instance across 
 *  many/all instantiations to allow "Singleton" type access to a resource 
 *  without sacrificing the benefits of dependency injection or using "evil"
 *  static/global references.
 * 
 *  The Provider recursively instantiates dependency objects automatically.
 *  For example, if class A has a dependency on class B and class B depends
 *  on class C, the Provider will first provision an instance of class B
 *  with the necessary dependencies in order to provision class A with an
 *  instance of B.
 * 
 *  ### BASIC PROVISIONING
 * 
 *  ##### No Dependencies
 * 
 *  If a class constructor specifies no dependencies there's absolutely no point
 *  in using the Provider to generate it. However, for the sake of completeness
 *  consider that you can do the following and get equivalent results:
 * 
 *  ```php
 *  $obj1 = new Namespace\MyClass;
 *  $obj2 = $provider->make('Namespace\MyClass');
 *  var_dump($obj1 === $obj2); // true
 *  ```
 * 
 *  ##### Concrete Typehinted Dependencies
 * 
 *  If a class requires only concrete dependencies you can use the Provider to
 *  inject it without specifying any injection definitions. So, for example, in
 *  the following scenario you can use the Provider to automatically provision
 *  `MyClass` with the required `DepClass` instance:
 * 
 *  ```php
 *  class DepClass
 *  {
 *  }
 * 
 *  class AnotherDep
 *  {
 *  }
 * 
 *  class MyClass
 *  {
 *      public $dep1;
 *      public $dep2;
 *      public function __construct(DepClass $dep1, AnotherDep $dep2)
 *      {
 *          $this->dep1 = $dep1;
 *          $this->dep2 = $dep2;
 *      }
 *  }
 * 
 *  $myObj = $provider->make('MyClass');
 *  var_dump($myObj->dep1 instanceof DepClass); // true
 *  var_dump($myObj->dep2 instanceof AnotherDep); // true
 *  ```
 *  
 *  This method will scale to any number of typehinted class dependencies
 *  specified in `__construct` methods.
 *  
 *  ###### Scalar Dependencies
 *  
 *  The design decision was explicitly made to disallow the specification of
 *  non-object dependency parameters. Such values are usually a failure to correctly
 *  implement recognized OOP design principles. Further, objects don't really
 *  "depend" on scalar values as they don't expose any functionality. If this
 *  behavior creates a problem in your application it may be worthwhile to
 *  reconsider how you're attacking your current problem.
 *  
 *  ### ADVANCED PROVISIONING
 *  
 *  The provider cannot instantiate a typehinted abstract class or interface without
 *  without a bit of help. This is where injection definitions come in.
 * 
 *  ##### Non-Concrete Dependencies
 *  
 *  The Provider allows you to define the class names it should use to provision
 *  objects with non-concrete method signatures. Consider:
 *  
 *   ```php
 *  interface DepInterface
 *  {
 *      public function doSomething();
 *  }
 *  
 *  class DepClass implements DepInterface
 *  {
 *      public function doSomething()
 *      {
 *      }
 *  }
 *  
 *  class MyClass
 *  {
 *      protected $dep;
 *      public function __construct(DepInterface $dep)
 *      {
 *          $this->dep = $dep;
 *      }
 *  }
 *  
 *  $provider->define('MyClass', ['dep' => 'DepClass']);
 *  $myObj = $provider->make('MyClass');
 *  var_dump($myObj instanceof MyClass); // true
 *  ```
 *  
 *  Custom injection definitions can also be specified using an instance
 *  of the requisite class, so the following would work in the same manner as
 *  above:
 *  
 *  ```php
 *  $provider->define('MyClass', [new DepClass]);
 *  $myObj = $provider->make('MyClass');
 *  var_dump($myObj instanceof MyClass); // true
 *  ```
 *  
 *  @category   Artax
 *  @package    Core
 *  @author     Daniel Lowrey <rdlowrey@gmail.com>
 *  Modifications made by Paul Young.
 */
class Provider implements Iface\Provider
{
	/** @property $definitions
	 *  \array of custom class instantiation parameters
	 */
	protected $definitions = array();

	/** @property $shared
	 *  \array of dependencies shared across the lifetime of the container.
	 */
	protected $shared = array();

	/** @property $reflectionCache
	 *  \array of cached reflected classes and constructor parameters.
	 */
	protected $reflectionCache;

	/******************/
	/* Public Methods */
	/******************/
	
	/** Create a new instance, auto-injecting the dependencies where possible.
     *  @param \string $class  Class name.
     *  @param \array  $definition Optional array specifying custom
     *  instantiation parameters for construction.
     *  @return \object An object created using dependency-injection.
     */
    public function build($class, Array $definition=NULL)
    {
	    if (!isset($definition) && isset($this->definitions[$class]))
        {
	        $definition = $this->definitions[$class];
        }
	        
        return $this->getInjectedInstance($class, $definition);
    }

    /** Defines custom instantiation parameters for the specified class.
     * 
     *  @param \string $class      Class name.
     *  @param \mixed  $definition An array specifying an ordered list of custom
     *  class names or an instance of the necessary class.
     */
    public function define($class, Array $definition)
    {
        $this->definitions[$class] = $definition;
    }

    /** Get a shared object using auto-injected dependencies where possible.
     *  @param \string $class  Class name.
     *  @param \array  $definition Optional array specifying custom
     *  instantiation parameters for construction.
     *  @return \object The shared object for the class and construction
     *  parameter combination.
     */
    public function get($class, Array $definition=NULL)
    {
	    if (!isset($definition) && isset($this->definitions[$class]))
        {
	        $definition = $this->definitions[$class];
        }

	    // Try to return an already created object with the same definition.
	    foreach (self::shared[$class] as $sharedEntry)
	    {
		    if ($sharedEntry['Definition'] === $definition)
		    {
			    return $sharedEntry['Object'];
		    }
	    }

	    // It has not been created yet, so we must create it now.
        $object = $this->getInjectedInstance($class, $definition);
        $this->shared[$class] = array('Definition' => $definition,
                                      'Object'     => $object);
        return $object;
    }
    
	/*********************/
	/* Protected Methods */
	/*********************/
    
    /**
     * Generate dependencies for a class without an injection definition
     * 
     * @param string $class Class name
     * @param array  $args  An array of ReflectionParameter objects
     * 
     * @return array Returns an array of dependency instances to inject
     * @throws LogicException If a provision attempt is made for a class whose
     *                        constructor specifies neither a typehint or NULL
     *                        default value for a parameter.
     * @used-by Provider::getInjectedInstance
     */
    protected function getDepsSansDefinition($class, array $args)
    {
	    $deps = array();
        
        for ($i=0; $i<count($args); $i++)
        {
	        if ($param = $args[$i]->getClass())
            {
                $deps[] = $this->make($param->name);
            }
	        elseif ($args[$i]->isDefaultValueAvailable()
	                && NULL === $args[$i]->getDefaultValue())
	        {
                $deps[] = NULL;
            }
	        else
	        {
                throw new LogicException(
                    "Cannot provision $class::__construct: no typehint ".
                    'or default NULL value specified at argument ' . ($i+1));
	        }
        }
        
        return $deps;
    }    
    
    /**
     * Generate dependencies for a class using an injection definition
     * 
     * @param \string $class Class name.
     * @param \array  $ctorParams  An array of ReflectionParameter objects.
     * @param \array  $def   An array specifying dependencies required for
     *                      object instantiation.
     * 
     * @return array Returns an array of dependency instances to inject
     * @throws ProviderDefinitionException If a provisioning attempt is made
     *                                     using an invalid injection definition
     * @used-by Provider::getInjectedInstance
     */
    protected function getDepsWithDefinition($class, $ctorParams, $def)
    {
	    $deps = array();
        
        foreach ($ctorParams as $param)
        {
            if (isset($def[$param->name]))
            {
	            $deps[] = is_string($def[$param->name]) && $param->getClass()
		            ? $this->make($def[$param->name])
		            : $def[$param->name];
            }
            elseif ($reflCls = $param->getClass())
            {
                $deps[] = $this->make($reflCls->name);
            }
            elseif ($param->isDefaultValueAvailable())
            {
                $deps[] = $param->getDefaultValue();
            }
            else
            {
                $deps[] = NULL;
            }
        }
        
        return $deps;
    }
    
    /**
     * Return an instantiated object subject to user-specified definitions
     * 
     * Note that reflected classes and their constructor parameters are 
     * cached to avoid needlessly reflecting the same classes over and over.
     * 
     * @param string $class Class name
     * @param array  $def   An array specifying dependencies required for
     *                      object instantiation
     * 
     * @return mixed Returns A dependency-injected object
     * @throws LogicException If the class being provisioned doesn't exist and
     *                        can't be autoloaded
     * @uses Provider::getDepsWithDefinition
     * @uses Provider::getDepsSansDefinition
     */
    protected function getInjectedInstance($class, $def)
    {
        if (isset($this->reflectionCache[$class]))
        {
            $params = $this->reflectionCache[$class]['ctor'];
            $refl   = $this->reflectionCache[$class]['class'];
        }
        else
        {
            try
            {
                $refl = new ReflectionClass($class);
                $this->reflectionCache[$class]['class'] = $refl;
            }
            catch (ReflectionException $e)
            {
                throw new LogicException(
	                "Provider instantiation failure: `$class` doesn't exist".
	                ' and cannot be found using the registered autoloaders.');
            }
            
            if ($ctor = $refl->getConstructor())
            {
                $params = $ctor->getParameters();
            }
            else
            {
                $params = NULL;
            }
            
            $this->reflectionCache[$class]['ctor'] = $params;
        }
        
        if (!$params)
        {            
	        return new $class;
        }
        else
        {
            $deps = (NULL === $def)
                ? $this->getDepsSansDefinition($class, $params)
                : $this->getDepsWithDefinition($class, $params, $def);
            
            return $refl->newInstanceArgs($deps);
        }
    }
}
// EOF