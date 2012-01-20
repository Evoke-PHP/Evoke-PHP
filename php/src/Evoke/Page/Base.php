<?php
namespace Evoke\Page;
/// Create the app object and run the page with the load method.
abstract class Base
{
   protected $app;

   public function __construct()
   {
      $this->app = new \Evoke\Core\App();
   }

   /********************/
   /* Abstract Methods */
   /********************/

   /// Load the page.
   abstract public function load();
}
// EOF