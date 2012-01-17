<?php
namespace Evoke;
class Exception_Base extends \Exception
{
   private $detailed = false;
   
   public function __construct(
      $method='', $message='', \Exception $previous=NULL, $code=0)
   {
      if (empty($method))
      {
	 $msg = $message;
      }
      elseif (empty($message))
      {
	 $msg = $method;
      }
      else
      {
	 $msg = $method . ': ' . $message;
      }
      
      parent::__construct($msg, $code, $previous);
   }

   /******************/
   /* Public Methods */
   /******************/

   public function setDetailedOutput($detailed = true)
   {
      $this->detailed = $detailed;
   }
   
   /// Return a string representation of the exception.
   public function __toString()
   {
      $str = 'exception \'' . get_class($this) . '\' with message \'' .
	 $this->message . '\' in ' . $this->getFile() . ': ' .
	 $this->getLine() . "\nStack trace:\n";

      if (!$this->detailed)
      {
	 $str .= $this->getTraceAsString();
      }
      else
      {
	 $str .= $this->detailedString();
      }
      
      $previous = $this->getPrevious();
      
      if (isset($previous))
      {
	 $str .= "\n\n" . (string)($previous);
      }

      return $str;
   }
   
   /*******************/
   /* Private Methods */
   /*******************/

   /** Get the full stack trace string with all call arguments.  It should be
    *  similar to the inbuilt getTraceAsString, but the output is exuberant! No
    *  elipsis (...) is used for hiding any information
    */
   private function detailedString()
   {
      echo 'DET';
      $str = '';
      
      $stack = $this->getTrace();
      
      foreach ($stack as $layer => $entry)
      {
	 $str .= "#" . $layer . " ";
	 
	 if (isset($entry['file']))
	 {
	    $str .= $entry['file'];
	 }
	 
	 if (isset($entry['line']))
	 {
	    $str .= "(" . $entry['line'] . "):";
	 }
	 
	 if (isset($entry['class']))
	 {
	    $str .= " " .  $entry['class'];
	 }
	 
	 if (isset($entry['type']))
	 {
	    $str .= $entry['type'];
	 }
	 
	 if (isset($entry['function']))
	 {
	    $str .= $entry['function'] . "(";
	    
	    if (isset($entry['args']) && count($entry['args']) > 0)
	    {
	       $argList = '';
	       
	       foreach ($entry['args'] as $arg)
	       {
		  $argList .= print_r($arg, true) . ', ';
	       }
	       
	       rtrim($argList, ', ');
	       
	       $str .= $argList;
	    }
	    
	    $str .= ")";
	 }

	 $str .= "\n";
      }

      return $str;
   }
}
// EOF