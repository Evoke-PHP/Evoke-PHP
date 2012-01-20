<?php
namespace Evoke\Core\Iface;

interface Handler
{ 
   /// Register the handler.
   public function register();

   /// Unregister the handler.
   public function unregister();
}
// EOF