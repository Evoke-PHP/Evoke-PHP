<?php
/// The system shutdown handler.
class Evoke_Handler_Shutdown extends Evoke_Handler
{
   protected $setup;
   
   public function __construct(Array $setup)
   {
      $this->setup = array_merge(
	 array('Administrator_Email'       => NULL,
	       'Detailed_Insecure_Message' => NULL),
	 $setup);

      if (!is_string($this->setup['Administrator_Email']))
      {
	 throw new InvalidArgumentException(
	    __METHOD__ . ' requires Administrator_Email to be a string');
      }
      
      if (!is_bool($this->setup['Detailed_Insecure_Message']))
      {
	 throw new InvalidArgumentException(
	    __METHOD__ . ' requires Detailed_Insecure_Message to be boolean');
      }

      $this->register('register_shutdown_function', array($this, 'handler'));
   }

   /******************/
   /* Public Methods */
   /******************/

   public function handler()
   {
      $err = error_get_last();

      if (!isset($err))
      {
	 return;
      }

      $handledErrorTypes = array(
	 E_USER_ERROR      => 'USER ERROR',
	 E_ERROR           => 'ERROR',
	 E_PARSE           => 'PARSE',
	 E_CORE_ERROR      => 'CORE_ERROR',
	 E_CORE_WARNING    => 'CORE_WARNING',
	 E_COMPILE_ERROR   => 'COMPILE_ERROR',
	 E_COMPILE_WARNING => 'COMPILE_WARNING');
      
      if (!isset($handledErrorTypes[$err['type']]))
      {
	 return;
      }

      if (!headers_sent())
      {
	 header('HTTP/1.1 500 Internal Server Error');
      }
      
      $title = 'Fatal Error';
      $message = 'This is an error that we were unable to handle.  Please ' .
	 'tell us any information that could help us avoid this error in the ' .
	 'future.  Useful information such as the date, time and what you ' .
	 'were doing when the error occurred should help us fix this.';

      if (!empty($this->setup['Administrator_Email']))
      {
	 $message .= "<br>\n<br>\n" .
	    'Email us at ' . $this->setup['Administrator_Email'];
      }
      
      if ($this->setup['Detailed_Insecure_Message'])
      {
	 $message .= "<br>\n<br>\n" .
	    'PHP [' . $handledErrorTypes[$err['type']] . '] ' .
	    $err['message'] . "<br>\n" .
	    ' in file ' . $err['file'] . ' at ' . $err['line'];
      }

      $errorDocument =
	 '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" ' . 
	 '"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">' . "\n" .
	 '<html xmlns="http://www.w3.org/1999/xhtml">' . "\n" .
         '   <head>' . "\n" .
	 '      <title>' . $title . '</title>' . "\n" .
	 '      <link type="text/css" href="/csslib/global.css" ' .
	 'rel="stylesheet"></link>' . "\n" .
	 '   </head>' . "\n" .
	 '   <body>' . "\n" .
	 '      <div class="Error_Handler Message_Box System">' . "\n" .
	 '         <div class="Title">' . $title . '</div>' . "\n" .
	 '         <div class="Description">' . $message . '</div>' . "\n" .
	 '      </div>' . "\n" .
	 '   </body>' . "\n" .
	 '</html>';
	 
      echo $errorDocument;
   }
}
// EOF