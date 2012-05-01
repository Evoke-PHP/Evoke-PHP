<?php
namespace Evoke\Processing;

use Evoke\Iface;

/// A class for doing no processing.
class None extends \Evoke\Processing
{
	/** Construct a no processing object.
	 *  @param eventManager  @object Event Manager object.
	 *  @param requestKeys   @array  The request keys we are processing.
	 *  @param matchRequired @bool   Whether a match is required.
	 *  @param uniqueMatch   @bool   Whether a unique match is required.
	 */
	final public function __construct(
		Iface\EventManager $eventManager,
		Array              $requestKeys   = array(),
		/* Bool   */       $matchRequired = false,
		/* Bool   */       $uniqueMatch   = true)
	{
		parent::__construct($eventManager, '', 'None', $requestKeys,
		                    $matchRequired, $uniqueMatch);
	}
   
	/******************/
	/* Public Methods */
	/******************/

	public function getRequest()
	{
		return array();
	}

	/// Get the Request Method so that no processing can be done.
	public function getRequestMethod()
	{
		return $this->requestMethod . ' AND THIS TO MAKE IT NOT MATCH!';
	}
}
// EOF