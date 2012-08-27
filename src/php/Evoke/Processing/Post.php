<?php
/**
 * POST Processing
 *
 * @package Processing
 */
namespace Evoke\Processing;

/**
 * Processing for $_POST.
 */
class Post extends Processing
{
	/**
	 * Construct a Post Processing object.
	 *
	 * @param mixed[] Callbacks.
	 * @param bool    MatchRequired.
	 * @param bool    Whether a Unique Match is required.
	 * @param string  Request Method.
	 */
	public function __construct(Array        $callbacks,
	                            /* Bool   */ $matchRequired = true,
	                            /* Bool   */ $uniqueMatch   = true,
	                            /* String */ $requestMethod = 'POST')
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
		if (empty($_POST))
		{
			return array('' => '');
		}

		return $_POST;
	}
}
// EOF