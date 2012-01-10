<?php
/** Container provides the single interface to object creation.
 *  It should be used to create every single object and shared object in the
 *  system.  While testing this should be the only class requiring stubbing.
 */
class Container
{
   protected static $shared = array();

   /******************/
   /* Public Methods */
   /******************/

   /** Get an object with the specified parameters.  This is the standard way
    *  to instantiate objects with the Evoke framework.  Using this method
    *  decouples object creation from your code.  This makes it easy to test
    *  your code as it is not tightly bound to the objects that it creates.
    *
    *  @param class \string The class of object to create.
    *  @param params \array Parameters to pass to the constructor.
    *  \return The object that has been created.
    */
   public function getNew($class, $params=array())
   {
      return new $class($params);
   }
   
   /** Get a shared object with the specified parameters.  The same object is
    *  returned for repeated calls with identical class and parameters.  This
    *  should be used for objects that will be shared throughout the system.
    *  There is a small overhead to find the shared object.  Few objects should
    *  be created as shared, so this overhead should remain small.
    *
    *  @param class \string The class of object to create or return.
    *  @param params \array Parameters to pass to the constructor.
    *  \return The shared object that has been retrieved or created (if it
    *  didn't exist.)
    */
   public function getShared($class, $params=array())
   {
      if (!isset(self::$shared[$class]))
      {
	 self::$shared[$class] = array();
      }
      
      foreach (self::$shared[$class] as $entry)
      {
	 if ($entry['Params'] === $params)
	 {
	    return $entry['Object'];
	 }
      }

      // We have not returned yet, it must be the first creation.
      $obj = new $class($params);
      self::$shared[$class][] = array('Object' => $obj,
				      'Params' => $params);
      return $obj;
   }

   /** Get a new object with a variable number of construction arguments. These
    *  arguments are described below in order:
    *
    *  \verbatim
       Class                   - The name of the class to be constructed.
       Construction Parameters - A variable number of construction parameters.
       \endverbatim
    *  \return The object that has been created.
    */
   public function getNewVarArgs(/* Var Args */)
   {
      if (func_num_args() === 0)
      {
	 throw new BadMethodCallException(
	    __METOHD__ . ' needs at least one argument.');
      }
      
      $args = func_get_args();
      $class = array_shift($args);
      
      $objClass = new ReflectionClass($class);
      return $objClass->newInstanceArgs($args);
   }
      
   /** Enable a string conversion for the object that gives a summary while
    *  avoiding circular references.
    */
   public function __toString()
   {
      $details = __CLASS__ . ' shared objects: ' . "\n";
      $details .= '  Count   Object' . "\n";
      $lineFormat = '  %5d   %s' . "\n";
      
      foreach (self::$shared as $classKey => $values)
      {
	 $details .= sprintf($lineFormat, count($values), $classKey);
      }

      $details .= 'End Container';
      
      return $details;
   }
}
// EOF