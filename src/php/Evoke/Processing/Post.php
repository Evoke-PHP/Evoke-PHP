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
	 * @param string  Request Method.
	 * @param bool    MatchRequired.
	 * @param bool    UniqueMatch.
	 */
	public function __construct(Array        $callbacks,
	                            /* String */ $requestMethod = 'POST',
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