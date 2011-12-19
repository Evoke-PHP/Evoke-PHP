<?php

class System_Handler_Shutdown extends System_Handler
{ 
   public function __construct()
   {
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

      $title = 'Fatal Error';
      $message = 'The administrator has been notified of this error.  ' .
	 'Sorry, we will fix this.';
      
      if (strtoupper(php_uname('n')) === 'BERNIE')
      {
	 $message .=
	    '  PHP [' . $handledErrorTypes[$err['type']] . '] ' .
	    $err['message'] . ' in file ' . $err['file'] . ' at ' .
	    $err['line'];

	 /// \todo Choose which one we want.
	 // $detailed = false;
	 $detailed = true;
	 
	 if ($detailed)
	 {
	    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

	    if (count($backtrace) > 1)
	    {
	       $message .= var_export($backtrace, true);
	    }
	 }
      }

      $c = new Container();
      $xwr = $c->getShared(
	 'XWR',
	 array('Translator' => $c->getShared(
		  'Translator',
		  array('Session_Manager' => $c->getShared(
			   'Session_Manager',
			   array('Domain'  => 'Lang',
				 'Session' => $c->getShared('Session')))))));

      $css = array('link',
		   array('href' => '/styleslib/global.css',
			 'rel'  => 'stylesheet',
			 'type' => 'text/css'));
      
      $messageBox = array(
	 'div',
	 array('class' => 'Error_Handler Message_Box System'),
	 array('Children' => array(
		  array('div',
			array('class' => 'Title'),
			array('Text' => $title)),
		  array('div',
			array('class' => 'Description'),
			array('Text' => $message)))));
      $xwr->write(
	 array('html',
	       array(),
	       array('Children' => array(
			array('head',
			      array(),
			      array('Children' => array($css))),
			array('body',
			      array(),
			      array('Children' => array($messageBox)))))));
      $xwr->outputXHTML();
   }
}

// EOF