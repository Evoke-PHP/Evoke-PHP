<?php
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
	 */
	public function __construct(
		$method, $message='', $db=NULL, $previous=NULL, $code=0)
	{
		if (method_exists($db, 'errorCode') && $db->errorCode() != '00000' &&
		    method_exists($db, 'errorInfo'))
		{
			$message .= ' Error: ' . implode(' ', $db->errorInfo());
		}
			
		parent::__construct($method . $message, $code, $previous);
	}
}
// EOF