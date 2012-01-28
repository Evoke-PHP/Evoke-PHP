<?php
namespace Evoke\Core;
/** InstanceManager implements the interface to manage instances.  It is used to
 *  create objects and retrieve shared objects in the system.  Using an instance
 *  manager decouples code that would otherwise use the new operator and gives
 *  control for the creation of instances, avoiding nasty singleton type
 *  methods.
 *
 *  Calling `new foo()` or bar::getInstance in code makes that code require a
 *  foo class or a bar class with a getInstance method.  This is a tight
 *  coupling that can be avoided by using this class.  By using this class the
 *  only requirement created in code that creates or gets instances of objects
 *  is that it is always injected with an object that implements the
 *  InstanceManager interface.
 *
 *  Using this class for all of your objects and shared resources makes it easy
 *  to test your code (you won't need stubs) as you will be able to inject all
 *  of the required objects.
 */
class InstanceManager implements Iface\InstanceManager
{
   protected static $shared = array();

   /******************/
   /* Public Methods */
   /******************/

   /** Create an object and return it.
    *
    *  This is the standard way to instantiate objects with the Evoke framework.
    *  Using this method decouples object creation from your code.  This makes
    *  it easy to test your code as it is not tightly bound to the objects that
    *  it creates.
    *
    *  Params:
    *  -# Full class name (including full namespace).
    *  -  Construction parameters. (As many or few as you want)
    *
    *  \return The object that has been created.
    */
   public function create(/* Var Args */)
   {
      $numArgs = func_num_args();
      
      if ($numArgs === 0)
      {
	 throw new \BadMethodCallException(
	    __METHOD__ . ' needs at least one argument.');
      }
      
      $args = func_get_args();
      $className = array_shift($args);
      --$numArgs;

      if ($numArgs === 0)
      {
	 return new $className();
      }
      elseif ($numArgs === 1)
      {
	 return new $className(reset($args));
      }
      else
      {
	 $object = new \ReflectionClass($className);
	 return $object->newInstanceArgs($args);
      }
   }

   /** Get a shared object.  The same object is returned for repeated calls with
    *  identical parameters.  This should be used for objects that will be
    *  shared throughout the system.  These objects should not rely on any
    *  state (otherwise sharing them could cause problems).
    *
    *  There is a small overhead to find the shared object.  Few objects should
    *  be created as shared, so this overhead should remain small.
    *
    *  Params:
    *  -# Full class name (including full namespace).
    *  -  Construction parameters. (As many or few as you want)
    *
    *  \return The shared object that has been retrieved or created (if it
    *  didn't exist.)
    */
   public function get(/* Var Args */)
   {
      $numArgs = func_num_args();
      
      if ($numArgs === 0)
      {
	 throw new \BadMethodCallException(
	    __METHOD__ . ' needs at least one argument.');
      }
      
      $args = func_get_args();
      $className = array_shift($args);
      --$numArgs;

      if (!isset(self::$shared[$className]))
      {
	 self::$shared[$className] = array();
      }
      
      foreach (self::$shared[$className] as $entry)
      {
	 if ($entry['Args'] === $args)
	 {
	    return $entry['Object'];
	 }
      }
      
      // We have not returned yet, it must be the first creation.
      if ($numArgs === 0)
      {
	 $object = new $className();
      }
      elseif ($numArgs === 1)
      {
	 $object = new $className(reset($args));
      }
      else
      {
	 $object = new \ReflectionClass($className);
	 $object->newInstanceArgs($args);
      }
      
      self::$shared[$className][] = array('Object' => $object,
					  'Args'   => $args);
      return $object;
   }
}
// EOF