<?php


/// Create the app object and run the page with the main method.
abstract class Page
{
   protected $app;

   final public function __construct()
   {
      $this->app = new App();
      $this->main();
   }

   /********************/
   /* Abstract Methods */
   /********************/

   /** main should do everything that needs to be done, write it so that you can
    *  go and play table tennis.
    */
   abstract public function main();
}

// EOF