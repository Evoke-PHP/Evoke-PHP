<?php
namespace Evoke;
class Exception_DB extends Exception_Base
{ 
   public function __construct(
      $method, $message='', $db=NULL, $previous=NULL, $code=0)
   {
      $msg = $message;
      
      if (method_exists($db, 'errorCode') && $db->errorCode() != '00000' &&
	  method_exists($db, 'errorInfo'))
      {
	 $msg .= ' Error: ' . implode(' ', $db->errorInfo());
      }

      parent::__construct($method, $msg, $previous, $code);
   }
}
// EOF