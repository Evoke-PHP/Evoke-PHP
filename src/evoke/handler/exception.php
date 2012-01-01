<?php
/// The system exception handler.
class Evoke_Handler_Exception extends Evoke_Handler
{
   protected $detailed;
   protected $em;
   
   public function __construct(Array $setup)
   {
      $setup += array('Detailed_Insecure_Message' => NULL,
		      'Event_Manager'             => NULL);

      if (!is_bool($setup['Detailed_Insecure_Message']))
      {
	 throw new InvalidArgumentException(
	    __METHOD__ . ' requires Detailed_Insecure_Message to be boolean');
      }

      if (!$setup['Event_Manager'] instanceof Event_Manager)
      {
	 throw new InvalidArgumentException(
	    __METHOD__ . ' requires Event_Manager');
      }

      $this->detailed = $setup['Detailed_Insecure_Message'];
      $this->em = $setup['Event_Manager'];
      $this->register('set_exception_handler', array($this, 'handler'));
   }
   
   /******************/
   /* Public Methods */
   /******************/

   /** Handle uncaught exceptions for the system by logging information and
    *  displaying a generic notice to the user so that they are informaed of an
    *  error without exposing information that could be used for an attack.
    *
    *  @param uncaughtException An exception that was not caught in the system.
    */
   public function handler($uncaughtException)
   {
      try
      {
	 if (!headers_sent())
	 {
	    header('HTTP/1.1 500 Internal Server Error');
	 }
				
	 $this->em->notify('Log',
			   array('Level'   => LOG_CRIT,
				 'Message' => $uncaughtException->getMessage(),
				 'Method'  => __METHOD__));
	 $loggedError = true;
      }
      catch (Exception $raisedException)
      {
	 $loggedError = false;
      }

      if (isset($_GET['l']) && ($_GET['l'] === 'ES'))
      {
	 $title = 'Error de Sistema';

	 if ($loggedError)
	 {
	    $message = 'El administrador ha estado notificado del error.  ' .
	       'Perdon, vamos a arreglarlo.';
	 }
	 else
	 {
	    $message =
	       'No pudimos notificar el administrador de esta problema.  ' .
	       'Por favor llamanos, queremos arreglarlo.';
	 }
      }
      else
      {
	 $title = 'System Error';

	 if ($loggedError)
	 {
	    $message = 'The administrator has been notified of this error.  ' .
	       'Sorry, we will fix this.';
	 }
	 else
	 {
	    $message =
	       'The administrator could not be notified of this error.  ' .
	       'Please call us, we want to fix this error.';
	 }
      }

      // Provide extended details for development servers.
      if ($this->detailed)
      {
	 $message .= "<br>\n<br>\n" .
	    preg_replace('/\n/', '<br>' . "\n", (string)($uncaughtException));
      }
      
      $errorDocument =
	 '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" ' . 
	 '"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">' . "\n" .
	 '<html xmlns="http://www.w3.org/1999/xhtml">' . "\n" .
         '   <head>' . "\n" .
	 '      <title>Error</title>' . "\n" .
	 '      <link type="text/css" href="/csslib/global.css" ' .
	 'rel="stylesheet"></link>' . "\n" .
	 '   </head>' . "\n" .
	 '   <body>' . "\n" .
	 '      <div class="Exception_Handler Message_Box System">' . "\n" .
	 '         <div class="Title">' . $title . '</div>' . "\n" .
	 '         <div class="Description">' . $message . '</div>' . "\n" .
	 '      </div>' . "\n" .
	 '   </body>' . "\n" .
	 '</html>';
	 
      echo $errorDocument;
   }
}
// EOF