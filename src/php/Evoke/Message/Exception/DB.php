<?php
/**
 * DB Exception
 *
 * @package Message
 */
namespace Evoke\Message\Exception;

/**
 * DB Exception
 *
 * An extended exception class for a DB API that provides error information.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Message
 */
class DB extends \Exception
{
	/**
	 * Create a Database exception class that captures the errorCode and
	 * errorInfo from the database that has thrown an exception.
	 *
	 * @param string    Method.
	 * @param string    Message.
	 * @param mixed     Database object.
	 * @param Exception Previous exception.
	 * @param int       Error Code.
	 */
	public function __construct(/* String    */ $method,
	                            /* String    */ $message  = '',
	                            /* Object    */ $database = NULL,
	                            /* Exception */ $previous = NULL,
	                            /* int       */ $code     = 0)
	{
		if (method_exists($database, 'errorCode') &&
		    $database->errorCode() != '00000' &&
		    method_exists($database, 'errorInfo'))
		{
			$message .= ' Error: ' . implode(' ', $database->errorInfo());
		}
			
		parent::__construct($method . $message, $code, $previous);
	}
}
// EOF