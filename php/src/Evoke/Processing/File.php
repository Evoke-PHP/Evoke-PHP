<?php
namespace Evoke\Processing;

class File extends \Evoke\Processing
{
	/** @property $requestPrefix
	 *  @string Prefix to the file request.
	 */
	protected $requestPrefix;

	/** @property $requestSeparator
	 *  @string Separator between the request prefix and the file specifier.
	 */
	protected $requestSeparator;

	/** Construct a File processing object.
	 *  @param callbacks   	    @array  The processing callbacks.
	 *  @param requestPrefix 	@string The prefix for the file request.
	 *  @param requestSeparator @string Separator between the prefix and file.
	 *  @param matchRequired 	@bool   Whether a match is required.
	 *  @param uniqueMatch   	@bool   Whether a unique match is required.
	 */
	public function __construct(
		Array              $callbacks,
		/* String */       $requestPrefix    = 'Input_File',
		/* String */       $requestSeparator = '_',
		/* Bool   */       $matchRequired    = true,
		/* Bool   */       $uniqueMatch      = true)
	{
		parent::__construct('FILE', $callbacks, $matchRequired, $uniqueMatch);

		$this->requestPrefix    = $requestPrefix;
		$this->requestSeparator = $requestSeparator;
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
			return $this->requestMethod;
		}
	}
   
	/*********************/
	/* Protected Methods */
	/*********************/

	/** Notify the matches which should now be processed.
	 *  @param callbacks   @array The callbacks that should be called.
	 *  @param requestData @array The data for the requests.
	 */
	protected function callRequests(Array $callbacks, Array $requestData)
	{
		/// @todo Fix this code.
		throw new RuntimeException(__METHOD__ . ' fix this.');
		// The request keys have already been formatted for us.
		foreach ($callbacks as $requestKey => $callback)
		{
			// Dispatch the processing using the event manager.
			$this->eventManager->notify(
				$this->eventPrefix . $requestKey,
				$data);	 
		}
	}
   
	/** Get the request keys that match the request data.
	 *  @param data @array The request data.
	 */
	protected function getRequestMatches($data)
	{
		/// @todo Fix this code.
		throw new RuntimeException(__METHOD__ . ' fix this.');
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
				$matches[$this->requestPrefix] = $entry;
			}
		}
      
		return $matches;
	}   
}
// EOF