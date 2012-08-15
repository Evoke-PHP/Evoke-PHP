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
	 * @param string  Request method.
	 * @param bool    MatchRequired.
	 * @param bool    UniqueMatch.
	 */
	public function __construct(Array        $callbacks,
	                            /* String */ $requestMethod = 'GET',
	                            /* Bool   */ $matchRequired = true,
	                            /* Bool   */ $uniqueMatch   = true)
	{
		parent::__construct(
			$callbacks, $requestMethod, $matchRequired, $uniqueMatch);
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