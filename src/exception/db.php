<?php

class Exception_DB extends Exception_Base
{ 
   public function __construct(
      $method, $message='', $db=NULL, $previous=NULL, $code=0)
   {
      $msg = $message;
      
      if (method_exists($db, 'errorCode') && method_exists($db, 'errorInfo'))
      {
	 if ($db->errorCode() != '00000')
	 {
	   $msg .= ' Error: ' . Utils::expand($db->errorInfo(), ' ');
	 }
      }

      parent::__construct($method, $msg, $previous, $code);
   }
}

// EOF