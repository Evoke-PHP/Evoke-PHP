<?php
namespace Evoke\Core\Init\Handler;

class Error implements \Evoke\Core\Iface\Handler
{
   protected $detailed;
   protected $em;
   protected $xwr;

   public function __construct(Array $setup)
   {
      $setup += array('Detailed_Insecure_Message' => NULL,
		      'EventManager'  	          => NULL,
		      'XWR'          	          => NULL);

      if (!is_bool($setup['Detailed_Insecure_Message']))
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires Detailed_Insecure_Message to be boolean');
      }

      if (!$setup['EventManager'] instanceof \Evoke\Core\EventManager)
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires EventManager');
      }

      if (!$setup['XWR'] instanceof \Evoke\Core\XWR)
      {
	 throw new \InvalidArgumentException(__METHOD__ . ' requires XWR');
      }
     
      $this->detailed = $setup['Detailed_Insecure_Message'];
      $this->em = $setup['EventManager'];
      $this->xwr = $setup['XWR'];
   }
   
   /******************/
   /* Public Methods */
   /******************/

   public function handler($errNo, $errStr, $errFile, $errLine)
   {
      // If the error code should not be reported return.
      if (!(error_reporting() & $errNo))
      {
	 // Do not allow PHP to report them either as PHP is telling us that
	 // they should not be reported.
	 return true;
      }
      
      $errType = array (E_ERROR             => 'Error',
			E_WARNING           => 'Warning',
			E_PARSE             => 'Parse',
			E_NOTICE            => 'Notice',
			E_CORE_ERROR        => 'Core Error',
			E_CORE_WARNING      => 'Core Warning',
			E_COMPILE_ERROR     => 'Compile Error',
			E_COMPILE_WARNING   => 'Compile Warning',
			E_USER_ERROR        => 'User Error',
			E_USER_WARNING      => 'User Warning',
			E_USER_NOTICE       => 'User Notice',
			E_STRICT            => 'Strict',
			E_DEPRECATED        => 'Deprecated',
			E_USER_DEPRECATED   => 'User Deprecated',
			E_RECOVERABLE_ERROR => 'Recoverable Error');

      if (isset($errType[$errNo]))
      {
	 $errTypeStr = $errType[$errNo];
      }
      else
      {
	 $errTypeStr = 'Unknown ' . $errNo;
      }
      
      switch ($errNo)
      {
      case E_COMPILE_ERROR:
      case E_CORE_ERROR:
      case E_CORE_WARNING:
      case E_ERROR:
      case E_PARSE:
	 throw new \OutOfBoundsException(
	    __METHOD__ . ' Unexpected error type: [' . $errTypeStr . '] ' .
	    $errStr . ' in file ' . $errFile . ' at ' . $errLine .
	    ' received. The PHP Manual for set_error_handler states that ' .
	    'errors of this type should not be received.');
	 break;

      case E_NOTICE:
      case E_RECOVERABLE_ERROR:
      case E_STRICT:
      case E_USER_ERROR:
      case E_USER_NOTICE:
      case E_USER_WARNING:
      case E_WARNING:
	 $this->writeError($errTypeStr, $errStr, $errFile, $errLine);
	 break;
	 
      default:
	 break;
      }

      // Allow PHP to perform its normal reporting of errors.  We have
      // augmented it with our own writing of the error which used our built-in
      // xml writing.
      return false;
   }

   public function register()
   {
      return set_error_handler(array($this, 'handler'));
   }

   public function unregister()
   {
      return restore_error_handler();
   }
   
   /*******************/
   /* Private Methods */
   /*******************/

   private function writeError($typeStr, $str, $file, $line)
   {
      try
      {
	 $message = 'Bootstrap Error handling [' . $typeStr . '] ' . $str .
	    ' in ' . $file . ' on ' . $line;

	 $this->em->notify(
	    'Log',
	    array('Level'   => LOG_WARNING,
		  'Message' => $message,
		  'Method'  => __METHOD__));
	 $loggedError = true;
      }
      catch (\Exception $e)
      {
	 $loggedError = false;
      }
            
      if (isset($_GET['l']) && ($_GET['l'] === 'ES'))
      {
	 $title = 'Error de Programa';

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
	 $title = 'Program Error';

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

      $descriptionElems = array(array('div',
				      array('class' => 'Message'),
				      array('Text' => $message)));

      if ($this->detailed)
      {
	 $descriptionElems[] = array(
	    'div',
	    array('class' => 'Breakpoint'),
	    array('Text'  => 'PHP [' . $typeStr . '] ' . $str . ' in file ' .
		  $file . ' at ' . $line));

	 $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	 array_shift($trace);
	 $traceElems = array();
	 
	 foreach ($trace as $level => $info)
	 {
	    $lineElems = array(array('span',
				     array('class' => 'Level'),
				     array('Text' => $level)));
	    
	    $info += array('file'     => '',
			   'line'     => '',
			   'function' => '',
			   'class'    => '',
			   'type'     => '');
	    
	    $text = '#' . $level . ' ' . $info['file'] . '(' .
	       $info['line'] . '): ' . $info['class'] .
	       $info['type'] . $info['function'];
	    
	    $traceElems[] = array(
	       'div',
	       array('class' => ($level % 2) ? 'Even' : 'Odd'),
	       array('Text'  => $text));
	 }

	 $descriptionElems[] = array(
	    'div',
	    array('class' => 'Trace'),
	    array('Children' => $traceElems));
      }
      
      $this->xwr->write(
	 array('div',
	       array('class' => 'Error_Handler Message_Box System'),
	       array('Children' => array(
			array('div',
			      array('class' => 'Title'),
			      array('Text' => $title)),
			array('div',
			      array('class' => 'Description'),
			      array('Children' => $descriptionElems))))));
      $this->xwr->outputXHTML();
   }
}
// EOF