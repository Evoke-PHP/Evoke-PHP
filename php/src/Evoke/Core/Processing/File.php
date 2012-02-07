<?php
namespace Evoke\Core\Processing;

class File extends Base
{ 
	public function __construct(Array $setup)
	{
		parent::__construct(
			array_merge(array('Event_Prefix'      => 'File.',
			                  'Match_Required'    => false,
			                  'Request_Method'    => 'FILE',
			                  'Request_Prefix'    => 'Input_File',
			                  'Request_Separator' => '_'),
			            $setup));
	}

	/******************/
	/* Public Methods */
	/******************/

	public function getRequest()
	{
		return $_FILES;
	}

	public function getRequestMethod()
	{
		if (isset($_FILES) && !empty($_FILES))
		{
			return $this->setup['Request_Method'];
		}
	}
   
	/*********************/
	/* Protected Methods */
	/*********************/

	/** Notify the matches which should now be processed.
	 *  @param requestKeys \array The requests that should be notified.
	 *  @param requestData \array The data for the requests.
	 */
	protected function callRequests(Array $requestKeys, Array $requestData)
	{
		// The request keys have already been formatted for us.
		foreach ($requestKeys as $requestKey => $data)
		{
			// Dispatch the processing using the event manager.
			$this->setup['Event_Manager']->notify(
				$this->setup['Event_Prefix'] . $requestKey,
				$data);	 
		}
	}
   
	/** Get the request keys that match the request data.
	 *  @param data \array The request data.
	 */
	protected function getRequestMatches($data)
	{
		$fullRequestPrefix = $this->setup['Request_Prefix'] .
			$this->setup['Request_Separator'];
		$fullPrefixLength = strlen($fullRequestPrefix);
		$matches = array();
      
		foreach ($data as $key => $val)
		{
			if (substr_compare(
				    $key, $fullRequestPrefix, 0, $fullPrefixLength) === 0)
			{
				$requestEntry = $val;
				$entry = array();

				// Translate the keys in the request entry to uppercase.
				foreach ($requestEntry as $k => $v)
				{
					$entry[mb_convert_case($k, MB_CASE_TITLE)] = $v;
				}
	    
				$entry['Table_Alias'] = substr($key, $fullPrefixLength);

				$matches[$this->setup['Request_Prefix']] = $entry;
			}
		}
      
		return $matches;
	}   
}
// EOF