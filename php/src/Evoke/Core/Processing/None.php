<?php
namespace Evoke\Core\Processing;
/// Handle the processing for something that need no processing.
class None extends Base
{
	final public function __construct($setup)
	{
		$setup += array('Event_Prefix'   => '',
		                'Match_Required' => false,
		                'Request_Keys'   => array(),
		                'Request_Method' => 'None',
		                'Unique_Match'   => true);
      
		parent::__construct($setup);
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
		return $this->setup['Request_Method'] . ' AND THIS TO MAKE IT NOT MATCH!';
	}
}
// EOF