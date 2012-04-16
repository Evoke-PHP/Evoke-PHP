<?php
namespace Evoke\Core;

/** The Evoke Provider class, derived from the Artax Provider Class File with
 *  permission from Daniel Lowrey.  Artax is an event-driven Application engine.
 *  It can be found here: https://github.com/rdlowrey/Artax-Core
 * 
 *  Note: This file was forked from the Atrax-Core dev branch: 648624a3cb.
 * 
 *  Only minor modifications have been made to the Artax provider class for use
 *  within Evoke.
 *  - The PHP 5.4 short array syntax has been replaced to keep Evoke to PHP 5.3.
 *  - ProviderDefinitionException replaced with LogicException.
 *  - Class names are not strtolower'ed.
 *
 *  @author     Daniel Lowrey <rdlowrey@gmail.com>
 *  Modifications made by Paul Young.
 */

use InvalidArgumentException,
	LogicException,
	ReflectionClass,
    ReflectionException,
    ArrayAccess,
    Traversable,
    StdClass;
  
/**
 * A dependency injection/provider class
 * 
 * The Provider is a dependency injection container existing specifically to
 * enable lazy instantiation of event listeners. `Provider::make` automatically
 * instantiates an instance of the given class name using reflection to
 * determine the class's constructor parameters. Non-concrete dependencies
 * may also be correctly instantiated using custom injection definitions.
 * 
 * The `Provider::share` method can be used to "recycle" an instance across 
 * many/all instantiations to allow "Singleton" type access to a resource 
 * without sacrificing the benefits of dependency injection or using "evil"
 * static/global references.
 * 
 * The Provider recursively instantiates dependency objects automatically.
 * For example, if class A has a dependency on class B and class B depends
 * on class C, the Provider will first provision an instance of class B
 * with the necessary dependencies in order to provision class A with an
 * instance of B.
 * 
 * ### BASIC PROVISIONING
 * 
 * ##### No Dependencies
 * 
 * If a class constructor specifies no dependencies there's absolutely no point
 * in using the Provider to generate it. However, for the sake of completeness
 * consider that you can do the following and get equivalent results:
 * 
 * ```php
 * $obj1 = new Namespace\MyClass;
 * $obj2 = $provider->make('Namespace\MyClass');
 * var_dump($obj1 === $obj2); // true
 * ```
 * 
 * ##### Concrete Typehinted Dependencies
 * 
 * If a class requires only concrete dependencies you can use the Provider to
 * inject it without specifying any injection definitions. So, for example, in
 * the following scenario you can use the Provider to automatically provision
 * `MyClass` with the required `DepClass` instance:
 * 
 * ```php
 * class DepClass
 * {
 * }
 * 
 * class AnotherDep
 * {
 * }
 * 
 * class MyClass
 * {
 *     public $dep1;
 *     public $dep2;
 *     public function __construct(DepClass $dep1, AnotherDep $dep2)
 *     {
 *         $this->dep1 = $dep1;
 *         $this->dep2 = $dep2;
 *     }
 * }
 * 
 * $myObj = $provider->make('MyClass');
 * var_dump($myObj->dep1 instanceof DepClass); // true
 * var_dump($myObj->dep2 instanceof AnotherDep); // true
 * ```
 * 
 * This method will scale to any number of typehinted class dependencies
 * specified in `__construct` methods.
 * 
 * ###### Scalar Dependencies
 * 
 * The design decision was explicitly made to disallow the specification of
 * non-object dependency parameters. Such values are usually a failure to correctly
 * implement recognized OOP design principles. Further, objects don't really
 * "depend" on scalar values as they don't expose any functionality. If this
 * behavior creates a problem in your application it may be worthwhile to
 * reconsider how you're attacking your current problem.
 * 
 * ### ADVANCED PROVISIONING
 * 
 * The provider cannot instantiate a typehinted abstract class or interface without
 * without a bit of help. This is where injection definitions come in.
 * 
 * ##### Non-Concrete Dependencies
 * 
 * The Provider allows you to define the class names it should use to provision
 * objects with non-concrete method signatures. Consider:
 * 
 *  ```php
 * interface DepInterface
 * {
 *     public function doSomething();
 * }
 * 
 * class DepClass implements DepInterface
 * {
 *     public function doSomething()
 *     {
 *     }
 * }
 * 
 * class MyClass
 * {
 *     protected $dep;
 *     public function __construct(DepInterface $dep)
 *     {
 *         $this->dep = $dep;
 *     }
 * }
 * 
 * $provider->define('MyClass', ['DepClass']);
 * $myObj = $provider->make('MyClass');
 * var_dump($myObj instanceof MyClass); // true
 * ```
 * 
 * Custom injection definitions can also be specified using an instance
 * of the requisite class, so the following would work in the same manner as
 * above:
 * 
 * ```php
 * $provider->define('MyClass', [new DepClass]);
 * $myObj = $provider->make('MyClass');
 * var_dump($myObj instanceof MyClass); // true
 * ```
 * 
 * @category   Artax
 * @package    Core
 * @author     Daniel Lowrey <rdlowrey@gmail.com>
 */
class Provider implements Iface\Provider
{
    /**
     * An array of custom class instantiation parameters
     * @var array
     */
	protected $definitions = array();
    
    /**
     * An array of dependencies shared across the lifetime of the container
     * @var array
     */
	protected $shared = array();
    
    /**
     * A cache of reflected classes and constructor parameters
     * @var array
     */
    protected $reflectionCache;
    
    /**
     * Defines custom instantiation parameters for the specified class
     * 
     * @param string $class      Class name
     * @param mixed  $definition An array specifying an ordered list of custom
     *                           class names or an instance of the necessary class
     * 
     * @return Provider Returns object instance for method chaining
     */
    public function define($class, array $definition)
    {
        $this->definitions[$class] = $definition;
        return $this;
    }
    
    /**
     * Defines multiple custom instantiation parameters at once
     * 
     * @param mixed $iterable The variable to iterate over: an array, StdClass
     *                        or ArrayAccess instance
     * 
     * @return int Returns the number of definitions stored by the operation.
     */
    public function defineAll($iterable)
    {
        if (!($iterable instanceof StdClass
            || is_array($iterable)
            || $iterable instanceof Traversable)
        ) {
            throw new InvalidArgumentException(
                'Argument 1 passed to addAll must be an array, StdClass or '
                .'implement Traversable '
            );
        }
        
        $added = 0;
        foreach ($iterable as $class => $definition) {
            $this->definitions[$class] = $definition;
            ++$added;
        }
        return $added;
    }
    
    /**
     * Determines if an injection definition exists for the given class name
     * 
     * @param string $class Class name
     * 
     * @return bool Returns TRUE if a definition is stored or FALSE otherwise
     */
    public function isDefined($class)
    {
        return isset($this->definitions[$class]);
    }
    
    /**
     * Determines if a given class name is marked as shared
     * 
     * @param string $class Class name
     * 
     * @return bool Returns TRUE if a shared instance is stored or FALSE otherwise
     */
    public function isShared($class)
    {
        return isset($this->shared[$class])
            || array_key_exists($class, $this->shared);
    }
    
    /**
     * Auto-injects dependencies upon instantiation of the specified class
     * 
     * @param string $class  Class name
     * @param array  $custom An optional array specifying custom instantiation 
     *                       parameters for this construction.
     * 
     * @return mixed A dependency-injected object
     * @throws LogicException On provisioning failure
     * @uses Provider::getInjectedInstance
     */
    public function make($class, array $custom = NULL)
    {
        if (isset($this->shared[$class])) {
            return $this->shared[$class];
        } elseif (array_key_exists($class, $this->shared)) {
            $shared = TRUE;
        } else {
            $shared = FALSE;
        }
        
        if (NULL !== $custom) {
            $definition = $custom;            
        } elseif (isset($this->definitions[$class])) {
            $definition = $this->definitions[$class];
        } else {
            $definition = NULL;
        }
        
        $obj = $this->getInjectedInstance($class, $definition);
        if ($shared) {
            $this->shared[$class] = $obj;
        }
        return $obj;
    }
    
    /**
     * Forces re-instantiation of a shared class the next time it is requested
     * 
     * Note that this does not un-share the class; it simply removes the
     * instance from the shared cache so that it will be recreated the next time
     * a provision request is made. If the specified class isn't shared to
     * begin with, no action will be taken.
     * 
     * @param string $class Class name
     * 
     * @return Provider Returns object instance for method chaining.
     */
    public function refresh($class)
    {
        if (isset($this->shared[$class])) {
            $this->shared[$class] = NULL;
        }
        return $this;
    }
    
    /**
     * Clear the injection definition for the specified class
     * 
     * Note that this operation will also remove any sharing definitions or
     * instances of the specified class.
     * 
     * @param string $class Class name
     * 
     * @return Provider Returns object instance for method chaining.
     */
    public function remove($class)
    {
        unset($this->definitions[$class]);
        unset($this->shared[$class]);
        
        return $this;
    }
    
    /**
     * Clear all injection definitions from the container
     * 
     * Note that this method also removes any shared definitions and instances.
     * 
     * @return Provider Returns object instance for method chaining.
     */
    public function removeAll()
    {
	    $this->definitions = array();
	    $this->shared = array();
        return $this;
    }
    
    /**
     * Stores a shared instance for the specified class
     * 
     * If no object instance is specified, the Provider will mark the class
     * name as "shared" and the next time the Provider is used to instantiate
     * the class it's instance will be stored and shared.
     * 
     * If an instance of the class is specified at Argument 2, it will be
     * stored and shared for calls to `Provider::make` for the specified class
     * until the shared instance is manually removed or refreshed.
     * 
     * @param string $class Class name
     * @param mixed  $obj   An instance of the specified class
     * 
     * @return Provider Returns object instance for method chaining
     * @throws InvalidArgumentException If passed object is not an instance 
     *                                  of the specified class
     */
    public function share($class, $obj = NULL)
    {
        if (NULL === $obj) {
            $this->shared[$class] = NULL;
            return $this;
        } elseif (!$obj instanceof $class) {
            $type = is_object($obj) ? get_class($obj) : gettype($obj);
            throw new InvalidArgumentException(
                'Parameter at '.get_class($this).'::share Argument 2 must be an '
                ."instance of $class: $type provided"
            );
        } else {
            $this->shared[$class] = $obj;
            return $this;
        }
    }
    
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
        
        for ($i=0; $i<count($args); $i++) {
            
            if ($param = $args[$i]->getClass()) {
                $deps[] = $this->make($param->name);
            } elseif ($args[$i]->isDefaultValueAvailable()
                && NULL === $args[$i]->getDefaultValue()
            ) {
                $deps[] = NULL;
            } else {
                throw new LogicException(
                    "Cannot provision $class::__construct: no typehint ".
                    'or default NULL value specified at argument ' . ($i+1)
                );
            }
        }
        
        return $deps;
    }    
    
    /**
     * Generate dependencies for a class using an injection definition
     * 
     * @param string $class Class name
     * @param array  $ctorParams  An array of ReflectionParameter objects
     * @param array  $def   An array specifying dependencies required for
     *                      object instantiation
     * 
     * @return array Returns an array of dependency instances to inject
     * @throws ProviderDefinitionException If a provisioning attempt is made
     *                                     using an invalid injection definition
     * @used-by Provider::getInjectedInstance
     */
    protected function getDepsWithDefinition($class, $ctorParams, $def)
    {
	    $deps = array();
        
        foreach ($ctorParams as $param) {
            if (isset($def[$param->name])) {
                $deps[] = is_string($def[$param->name]) && $param->getClass()
                    ? $this->make($def[$param->name])
                    : $def[$param->name];
            } elseif ($reflCls = $param->getClass()) {
                $deps[] = $this->make($reflCls->name);
            } elseif ($param->isDefaultValueAvailable()) {
                $deps[] = $param->getDefaultValue();
            } else {
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
        if (isset($this->reflectionCache[$class])) {
            $params = $this->reflectionCache[$class]['ctor'];
            $refl   = $this->reflectionCache[$class]['class'];
        } else {
            try {
                $refl = new ReflectionClass($class);
                $this->reflectionCache[$class]['class'] = $refl;
            } catch (ReflectionException $e) {
                throw new LogicException(
                    "Provider instantiation failure: `$class` doesn't exist".
                    ' and cannot be found using the registered autoloaders.'
                );
            }
            
            if ($ctor = $refl->getConstructor()) {
                $params = $ctor->getParameters();
            } else {
                $params = NULL;
            }
            $this->reflectionCache[$class]['ctor'] = $params;
        }
        
        if (!$params) {
            
            return new $class;
            
        } else {
            $deps = (NULL === $def)
                ? $this->getDepsSansDefinition($class, $params)
                : $this->getDepsWithDefinition($class, $params, $def);
            
            return $refl->newInstanceArgs($deps);
        }
    }
}
// EOF