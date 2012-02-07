<?php
namespace Evoke\Core\Exception;

class DB extends Base
{ 
	/** Create a Database exception class that captures the errorCode and
	 *  errorInfo from the database that has thrown an exception.
	 */
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