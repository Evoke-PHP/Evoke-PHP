<?php
namespace Evoke\Core\Iface;

interface InstanceManager
{
   /// Create a new object and return it.
   public function create(/* Var Args */);

   /** Get an object that is to be shared throughout the system (creating it if
    *  it if it has not yet been created).
    */
   public function get(/* Var Args */);
}
// EOF