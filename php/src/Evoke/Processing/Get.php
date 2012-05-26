<?php
namespace Evoke\Processing;

class Get extends \Evoke\Processing
{
	/** Construct a Get Processing object.
	 *  @param requestKeys   @array RequestKeys.
	 *  @param matchRequired @bool  MatchRequired.
	 *  @param uniqueMatch   @bool  UniqueMatch.
	 */
	public function __construct(Array      $requestKeys,
	                            /* Bool */ $matchRequired = true,
	                            /* Bool */ $uniqueMatch   = true)
	{
		parent::__construct('GET', $requestKeys, $matchRequired, $uniqueMatch);
	}

	/******************/
	/* Public Methods */
	/******************/

	public function getRequest()
	{
		$getRequest = $_GET;

		/// \todo Deal with the language from the get request properly.
		unset($getRequest['l']);
      
		if (empty($getRequest))
		{
			return array('' => '');
		}

		return $getRequest;
	}
}
// EOF