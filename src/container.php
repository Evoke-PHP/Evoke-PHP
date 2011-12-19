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

   /** Get an object with the specified parameters.
    *  @param class \string The class of object to create.
    *  @param params \array Parameters to pass to the constructor.
    */
   public function get($class, $params=array())
   {
      return new $class($params);
   }
   
   /** Get a shared object with the specified parameters.  The same object is
    *  returned for repeated calls with identical class and parameters.
    *  @param class \string The class of object to create or return.
    *  @param params \array Parameters to pass to the constructor.
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

   /// \todo Add variable arg construction methods.
   // $objClass = new ReflectionClass($class);
   // $obj = $objClass->newInstanceArgs($args);
   
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