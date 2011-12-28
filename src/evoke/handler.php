<?php

abstract class Evoke_Handler
{ 
   /******************/
   /* Public Methods */
   /******************/

   /** Register the handler.
    *  @param func The function to use to register the handler.
    *  @param handler The handler being registered.
    *  \returns \mixed The return result of the registration function.
    */
   protected function register($func, $handler)
   {
      return call_user_func($func, $handler);
   }
}

// EOF