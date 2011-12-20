<?php
require_once 'system/files.php';

/// The system exception handler.
class System_Handler_Exception extends System_Handler
{
   public function __construct()
   {
      $this->register('set_exception_handler', array($this, 'handler'));
   }
   
   /******************/
   /* Public Methods */
   /******************/

   public function handler($e)
   {
      try
      {
	 if (!headers_sent())
	 {
	    header('HTTP/1.1 500 Internal Server Error');
	 }
	 
	 $c = new Container();
	 $log = $c->getShared('Logger', array('App' => $c->getShared('App')));

	 $eventManager = $c->getShared('Event_Manager');
	 
	 $logFile = $c->getShared(
	    'Logger_File',
	    array('Event_Manager' => $eventManager,
		  'Filename'      => LOG_FILE,
		  'File_System'   => $c->getShared('File_System')));
				
	 $eventManager->notify(
	    'Log',
	    array('Level'  => LOG_CRIT,
		  'Message' => $e->getMessage(),
		  'Method' => __METHOD__));
	 $loggedError = true;
      }
      catch (Exception $raisedException)
      {
	 $loggedError = false;
      }
      
      if (strtoupper(php_uname('n')) === 'BERNIE')
      {
	 $details = (string)($e);
	 $details = preg_replace('/\n/', '<br>' . "\n", $details);
      }
      else
      {
	 $details = '';
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

      if (!empty($details))
      {
	 $message .= "<br>\n<br>\n" . $details;
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