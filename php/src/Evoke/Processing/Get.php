<?php
namespace Evoke\Processing;

class Get extends \Evoke\Processing
{
	/** Construct a Get processing object.
	 *  @param eventManager  @object Event Manager object.
	 *  @param requestKeys   @array  The request keys we are processing.
	 *  @param matchRequired @bool   Whether a match is required.
	 *  @param uniqueMatch   @bool   Whether a unique match is required.
	 */
	public function __construct(Iface\EventManager $eventManager,
	                            Array              $requestKeys,
	                            /* Bool   */       $matchRequired = true,
	                            /* Bool   */       $uniqueMatch   = true)
	{
		parent::__construct($eventManager, 'Get.', 'GET', $requestKeys,
		                    $matchRequired, $uniqueMatch);
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