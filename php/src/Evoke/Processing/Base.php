<?php
namespace Evoke\Processing;

use Evoke\Iface;

/** Processing Base Class
 *  This handles the routing of request information to processing callbacks
 *  via @ref Event_Manager::notify.  It is de-coupled from the request by
 *  the abstract method which gets the request data.  This allows the same
 *  processing to be performed for $_GET, $_POST or other forms of request
 *  that you may receive.
 *
 *  We assume that a request is received in an array.  We use the keys of
 *  this to distinguish the type of request so that it can be routed to the
 *  correct processing.  The use of request identifiers allows us to match the
 *  keys from the request and notify for the specific processing required.
 */
abstract class Base implements Iface\Processing
{
	/** @property $eventManager
	 *  EventManager @object
	 */
	protected $eventManager;

	/** @property $eventPrefix
	 *  @string Prefix to the event notification.
	 */
	protected $eventPrefix;

	/** @property $matchRequired
	 *  @bool Whether a key is required to match for processing.
	 */
	protected $matchRequired;

	/** @property $requestKeys
	 *  @array Keys that indicate the type of request received.
	 */
	protected $requestKeys;

	/** @property $uniqueMatch
	 *  @bool Whether only a single request type can be processed at a time.
	 */
	protected $uniqueMatch;

	/** Construct a Processing object.
	 *  @param eventManager  @object Event Manager object.
	 *  @param eventPrefix   @string Event prefix for the Event Manager.
	 *  @param requestMethod @string The request method that we are processing.
	 *  @param requestKeys   @array  The request keys we are processing.
	 *  @param matchRequired @bool   Whether a match is required.
	 *  @param uniqueMatch   @bool   Whether a unique match is required.
	 */
	public function __construct(Iface\EventManager $eventManager,
	                            /* String */       $eventPrefix,
	                            /* String */       $requestMethod,
	                            Array              $requestKeys,
	                            /* Bool   */       $matchRequired = true,
	                            /* Bool   */       $uniqueMatch   = true)
	{
		if (!is_string($eventPrefix))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires eventPrefix as string');
		}

		if (!is_string($requestMethod))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires requestMethod as string');
		}
		
		$this->eventManager  = $eventManager;
		$this->eventPrefix   = $eventPrefix;
		$this->matchRequired = $matchRequired;
		$this->requestKeys   = $requestKeys;
		$this->requestMethod = $requestMethod;
		$this->uniqueMatch   = $uniqueMatch;
		
		// Duplicate the request key values to the keys for easier diffing.
		if (!empty($this->requestKeys))
		{
			$this->requestKeys =
				array_combine($this->requestKeys, $this->requestKeys);
		}

		// By default we are connected for processing.
		$this->eventManager->connect('Request.Process', array($this, 'process'));
	}

	/******************/
	/* Public Methods */
	/******************/

	/// Process the request.
	public function process()
	{
		if ($this->getRequestMethod() !== mb_strtoupper($this->requestMethod))
		{
			return;
		}
      
		$requestData = $this->getRequest();
		$requestKeyMatches = $this->getRequestMatches($requestData);
		$this->checkMatches($requestKeyMatches, $requestData);

		if (!empty($requestKeyMatches))
		{
			$this->callRequests($requestKeyMatches, $requestData);
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
		foreach($requestKeys as $key => $val)
		{
			// We do not need the request type to be passed to the processing.
			$data = $requestData;
			unset($data[$key]);

			// Dispatch the processing using the event manager.
			$this->eventManager->notify($this->eventPrefix . $key, $data);
		}
	}

	/** Check to ensure that the matches we have conform to the expectations for
	 *  uniqueness and optionality.
	 *  @param matches \array The matches found in the request data.
	 *  @param requestData \mixed The request data.
	 */
	protected function checkMatches(Array $matches, $requestData)
	{
		if ($this->matchRequired && (count($matches) === 0))
		{
			$msg = 'Match_Required for Event_Prefix: ' .
				var_export($this->eventPrefix, true) . ' Request_Keys: ' .
				var_export(array_keys($this->requestKeys), true) .
				' with request data: ' . var_export($requestData, true);

			$this->eventManager->notify(
				'Log', array('Level'   => LOG_ERR,
				             'Message' => $msg,
				             'Method'  => __METHOD__));
	 
			throw new \RuntimeException(__METHOD__ . ' ' . $msg);
		}
		elseif ($this->uniqueMatch && count($matches) > 1)
		{
			$msg = 'Unique_Match required for Request_Keys: ' .
				var_export(array_keys($this->requestKeys)) .
				' with request data: ' . var_export($requestData, true);

			$this->eventManager->notify(
				'Log', array('Level'   => LOG_ERR,
				             'Message' => $msg,
				             'Method'  => __METHOD__));
		       
			throw new \RuntimeException(__METHOD__ . ' ' . $msg);
		}
	}

	/** Get the request keys that match the request data.
	 *  @param data \array The request data.
	 */
	protected function getRequestMatches($data)
	{
		return array_intersect_key($this->requestKeys, $data);
	}

	/** Get the request method.
	 */
	protected function getRequestMethod()
	{
		return mb_strtoupper($_SERVER['REQUEST_METHOD']);
	}
}
// EOF