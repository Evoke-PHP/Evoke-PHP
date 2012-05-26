<?php
namespace Evoke\Processing;

class Post extends \Evoke\Processing
{
	/** Construct a Post Processing object.
	 *  @param requestKeys   @array RequestKeys.
	 *  @param matchRequired @bool  MatchRequired.
	 *  @param uniqueMatch   @bool  UniqueMatch.
	 */
	public function __construct(Array      $requestKeys,
	                            /* Bool */ $matchRequired = true,
	                            /* Bool */ $uniqueMatch   = true)
	{
		parent::__construct('POST', $requestKeys, $matchRequired, $uniqueMatch);
	}

	/******************/
	/* Public Methods */
	/******************/

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