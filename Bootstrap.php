<?php
/**
 * Bootstrap
 *
 * An example bootstrap function.
 *
 * @package Bootstrap
 */
namespace Evoke\Bootstrap;

use DateTime,
	Evoke\Network\HTTP,
	Evoke\Service,
	Evoke\View,
	Evoke\Writer,
	XMLWriter;

/**
 * Bootstrap
 *
 * Example function for initializing a system.
 *
 * @param bool   Whether this is a development server.
 * @param string The filename of the log file to use.
 * @return HTTP\Response
 */
function bootstrap($isDevelopmentServer, $logFile)
{
	try
	{
		/************/
		/* Autoload */
		/************/
		$component = 'Autoload';
		$evokeDir = __DIR__ . '/src/php/';
		$autoloadDir = $evokeDir . 'Evoke/Service/Autoload/';
		require $autoloadDir . 'AutoloadIface.php';
		require $autoloadDir . 'PSR0Namespace.php';

		$autoloader = new Service\Autoload\PSR0Namespace(
			$evokeDir, 'Evoke\\');
		spl_autoload_register([$autoloader, 'load']);

		/***********/
		/* Logging */
		/***********/
		$component = 'Logging';
		$logging = new Service\Log\Logging(new DateTime);
		$logging->attach(new Service\Log\File($logFile));

		/************/
		/* Response */
		/************/
		$component = 'Response';
		$response = new HTTP\Response;

		/********************/
		/* Shutdown Handler */
		/********************/
		$component = 'Shutdown Handler';
		$viewShutdownMessageBox = new View\XHTML\MessageBox(
			['class' => 'Message_Box Shutdown']);
		$viewError = new View\XHTML\Error;
		$xhtmlWriter = new Writer\XML(new XMLWriter);
		$shutdownHandler = new Service\ShutdownHandler(
			'admin@bigthrow.com.au', $response, $isDevelopmentServer,
			$viewShutdownMessageBox, $xhtmlWriter, $viewError);
		register_shutdown_function([$shutdownHandler, 'handler']);

		/*********************/
		/* Exception Handler */
		/*********************/
		$component = 'Exception Handler';
		$viewExceptionMessageBox = new View\XHTML\MessageBox(
			['class' => 'Message_Box Exception']);
		$viewException = new View\XHTML\Exception;
		$exceptionHandler = new Service\ExceptionHandler(
			$response, $isDevelopmentServer, $viewExceptionMessageBox,
			$xhtmlWriter, $viewException);
		set_exception_handler([$exceptionHandler, 'handler']);

		/*****************/
		/* Error Handler */
		/*****************/
		$component = 'Error Handler';
		$errorHandler = new Service\ErrorHandler($logging);
		set_error_handler([$errorHandler, 'handler']);

		return $response;
	}
	catch(\Exception $e)
	{
		header('HTTP/1.1 500 Internal Server Error');
		die('System failure due to: ' . $component . '.' .
		    $isDevelopmentServer ? ' Exception: ' . $e->getMessage() : '');
	}
}
// EOF