<?php
/**
 * GET Processing
 *
 * @package Processing
 */
namespace Evoke\Processing;

/**
 * Processing for GET.
 */
class Get extends Processing
{
	/**
	 * Construct a Get Processing object.
	 *
	 * @param mixed[] Callbacks.
	 * @param bool    MatchRequired.
	 * @param bool    UniqueMatch.
	 * @param string  Request method.
	 */
	public function __construct(Array        $callbacks,
	                            /* Bool   */ $matchRequired = true,
	                            /* Bool   */ $uniqueMatch   = true,
	                            /* String */ $requestMethod = 'GET')
	{
		parent::__construct(
			$callbacks, $matchRequired, $uniqueMatch, $requestMethod);
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the request to process.
	 *
	 * @SuppressWarnings(PHPMD)
	 */
	public function getRequest()
	{
		$getRequest = $_GET;

		/// @todo Deal with the language from the get request properly.
		unset($getRequest['l']);
      
		if (empty($getRequest))
		{
			return array('' => '');
		}

		return $getRequest;
	}
}
// EOF