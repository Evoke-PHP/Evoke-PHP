<?php
namespace Evoke\Core;
/** ObjectHandler implements the interface to object creation.  It is important
 *  to create objects using an object from a class like this so that the code
 *  creating an object is not tightly coupled to an implementation of the class
 *  of object that it is creating.
 * 
 *  Testing code with 'new' is difficult, but testing it with
 *  $objectHandler->getNew('xxx') is easy.
 */
class ObjectHandler implements Iface\ObjectHandler
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
   public function getNew(/* Var Args */)
   {
      $numArgs = func_num_args();
      
      if ($numArgs === 0)
      {
	 throw new \BadMethodCallException(
	    __METOHD__ . ' needs at least one argument.');
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
   public function getShared(/* Var Args */)
   {
      $numArgs = func_num_args();
      
      if ($numArgs === 0)
      {
	 throw new \BadMethodCallException(
	    __METOHD__ . ' needs at least one argument.');
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