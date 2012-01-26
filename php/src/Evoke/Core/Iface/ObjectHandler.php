<?php
namespace Evoke\Core\Iface;

interface ObjectHandler
{
   /// Create a new object and return it.
   public function getNew(/* Var Args */);
   
   /** Retrieve an object that is to be shared throughout the system (or create
    *  it if it has not yet been created).
    */
   public function getShared(/* Var Args */);
}
// EOF