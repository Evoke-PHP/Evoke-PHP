<?php
namespace Evoke\Processing;

class Post extends \Evoke\Processing
{ 
	/** Construct a Post processing object.
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
		parent::__construct($eventManager, 'Post.', 'POST', $requestKeys,
		                    $matchRequired, $uniqueMatch);
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