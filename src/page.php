<?php
/// Create the app object and run the page with the main method.
abstract class Page
{
   protected $app;

   public function __construct()
   {
      $this->app = new App();
   }

   /********************/
   /* Abstract Methods */
   /********************/

   /// Load the page.
   abstract public function load();
}

// EOF