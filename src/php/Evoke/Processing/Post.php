<?php
namespace Evoke\Processing;

class Post extends Processing
{
	/** Construct a Post Processing object.
	 *  @param callbacks     @array Callbacks.
	 *  @param matchRequired @bool  MatchRequired.
	 *  @param uniqueMatch   @bool  UniqueMatch.
	 */
	public function __construct(Array      $callbacks,
	                            /* Bool */ $matchRequired = true,
	                            /* Bool */ $uniqueMatch   = true)
	{
		parent::__construct('POST', $callbacks, $matchRequired, $uniqueMatch);
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