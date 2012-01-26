<?php
namespace Evoke\Core\Processing;
/** Processing Base Class
 *  This handles the routing of request information to processing callbacks
 *  via \ref Event_Manager::notify.  It is de-coupled from the request by
 *  the abstract method which gets the request data.  This allows the same
 *  processing to be performed for $_GET, $_POST or other forms of request
 *  that you may receive.
 *
 *  We assume that a request is received in an array.  We use the keys of
 *  this to distinguish the type of request so that it can be routed to the
 *  correct processing.  The use of request identifiers allows us to match the
 *  keys from the request and notify for the specific processing required.
 */
abstract class Base
{
   protected $setup;
   
   /** Construct the processing object.
    *  @param setup \array The settings for the class:
    *  EventManager   - Event manager that we will notify about the request.
    *  Event_Prefix   - Prefix to the event notifications.
    *  Match_Required - Whether a key is required each time we are called.
    *  Request_Keys   - Keys that indicate the type of request received.
    *  Throw_On_Error - Whether an exception should be thrown on errors.
    *  Unique_Match   - Whether only a single request type can appear each time.
    */	   
   public function __construct(Array $setup)
   {
      $this->setup = array_merge(
	 array('EventManager'   => NULL,
	       'Event_Prefix'   => NULL,
	       'Match_Required' => true,
	       'Request_Keys'   => array(),
	       'Request_Method' => '',
	       'Unique_Match'   => true),
	 $setup);
      
      if (!$this->setup['EventManager'] instanceof \Evoke\Core\EventManager)
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires EventManager');
      }

      if (!is_string($this->setup['Event_Prefix']))
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires Event_Prefix as string');
      }

      // Duplicate the request key values to the keys for easier diffing.
      if (!empty($this->setup['Request_Keys']))
      {
	 $this->setup['Request_Keys'] =
	    array_combine($this->setup['Request_Keys'],
			  $this->setup['Request_Keys']);
      }

      // By default we are connected for processing.
      $this->setup['EventManager']->connect(
	 'Request.Process', array($this, 'process'));
   }

   /// Get the request information.
   abstract public function getRequest();

   /******************/
   /* Public Methods */
   /******************/

   public function getRequestMethod()
   {
      return mb_strtoupper($_SERVER['REQUEST_METHOD']);
   }
   
   /// Process the request and notify the event manager.
   public function process()
   {
      if ($this->getRequestMethod() !== mb_strtoupper(
	     $this->setup['Request_Method']))
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
	 $this->setup['EventManager']->notify(
	    $this->setup['Event_Prefix'] . $key,
	    $data);
      }
   }

   /** Check to ensure that the matches we have conform to the expectations for
    *  uniqueness and optionality.
    *  @param matches \array The matches found in the request data.
    *  @param requestData \mixed The request data.
    */
   protected function checkMatches(Array $matches, $requestData)
   {
      if ($this->setup['Match_Required'] && (count($matches) === 0))
      {
	 $msg = 'Match_Required for Event_Prefix: ' .
	    var_export($this->setup['Event_Prefix'], true) . ' Request_Keys: ' .
	    var_export(array_keys($this->setup['Request_Keys']), true) .
	    ' with request data: ' . var_export($requestData, true);

	 $this->setup['EventManager']->notify(
	    'Log', array('Level'   => LOG_ERR,
			 'Message' => $msg,
			 'Method'  => __METHOD__));
	 
	 throw new \RuntimeException(__METHOD__ . ' ' . $msg);
      }
      
      if ($this->setup['Unique_Match'] && count($matches) > 1)
      {
	 $msg = 'Unique_Match required for Request_Keys: ' .
	    var_export(array_keys($this->setup['Request_Keys'])) .
	    ' with request data: ' . var_export($requestData, true);

	 $this->setup['EventManager']->notify(
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
      return array_intersect_key($this->setup['Request_Keys'], $data);
   }
}
// EOF