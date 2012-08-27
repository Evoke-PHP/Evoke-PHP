<?php
/**
 * File Processing
 *
 * @package Processing
 */
namespace Evoke\Processing;

/**
 * Processing for $_FILE
 */
class File extends Processing
{
	/**
	 * Construct a File processing object.
	 *
	 * @param mixed[] The processing callbacks.
	 * @param bool    Whether a match is required.
	 * @param bool    Whether a unique match is required.
	 * @param string  The request method.
	 */
	public function __construct(Array        $callbacks,
	                            /* Bool   */ $matchRequired = true,
	                            /* Bool   */ $uniqueMatch   = true,
	                            /* String */ $requestMethod = 'FILE')
	{
		parent::__construct(
			$callbacks, $matchRequired, $uniqueMatch, $requestMethod);
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the request that is being processed.
	 *
	 * @SuppressWarnings(PHPMD)
	 */
	public function getRequest()
	{
		return $_FILES;
	}

	/**
	 * Get the request that is being processed.
	 *
	 * @SuppressWarnings(PHPMD)
	 */
	public function getRequestMethod()
	{
		return (isset($_FILES) && !empty($_FILES)) ?
			$this->requestMethod : parent::getRequestMethod();
	}
}
// EOF