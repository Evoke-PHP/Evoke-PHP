<?php


/// Model provides the basic implementation for a model.
abstract class Model implements Iface_Model
{ 
   protected $app;
   protected $em;
   protected $setup;
   
   public function __construct(Array $setup)
   {
      $this->setup = array_merge(
	 array('App'            => NULL,
	       'Connect_Events' => true,
	       'Data_Prefix'    => NULL,
	       'Event_Manager'  => NULL,
	       'Event_Prefix'   => 'Post.',
	       'Got_Data_Event' => 'Got_Data'),
	 $setup);

      $this->setup['App']->needs(
	 array('Instance'  => array(
		  'Event_Manager' => $this->setup['Event_Manager'])));

      $this->app =& $this->setup['App'];
      $this->em =& $this->setup['Event_Manager'];


      if ($this->setup['Connect_Events'])
      {
	 $this->connectEvents('Model.', array('Notify_Data' => 'notifyData'));

	 $processingEvents = $this->getProcessingEventMap();

	 if (!empty($processingEvents))
	 {
	    $this->connectEvents($this->setup['Event_Prefix'],
				 $processingEvents);
	 }

	 // The empty event is non-critical by default.
	 $this->em->setCritical($this->setup['Event_Prefix'] . '', false);
      }
   }
   
   /******************/
   /* Public Methods */
   /******************/
   
   /// Get the data for the model.
   public function getData()
   {
      return $this->offsetData(array());
   }

   /// Get the events used for processing in the model.
   public function getProcessingEvents()
   {
      $events = array_keys($this->getProcessingEventMap());

      // Add the no event to the list.
      $events[] = '';

      return $events;
   }
   
   /// Notify the data that the model has.
   public function notifyData()
   {
      $this->em->notify($this->setup['Got_Data_Event'], $this->getData());
   }
   
   /*********************/
   /* Protected Methods */
   /*********************/

   /** Connect events with the appropriate event name prefix to the specified
    *  methods of this model.
    *  @param prefix \string The event name prefix to use.
    *  @param events \array The event name to method name array.
    */
   protected function connectEvents($prefix, Array $events)
   {
      foreach ($events as $eventName => $method)
      {
	 $this->em->connect($prefix . $eventName, array($this, $method));
      }
   }

   /// Get a subset of the data at the specified prefix.
   protected function getAtPrefix($data, $prefix)
   {
      if (empty($prefix))
      {
	 return $data;
      }
      
      $ptr =& $data;

      try
      {
	 if (is_array($prefix))
	 {
	    foreach ($prefix as $p)
	    {
	       if (!isset($ptr[$p]))
	       {
		  throw new Exception();
	       }
	       
	       $ptr =& $ptr[$prefix];
	    }
	 }
	 else
	 {
	    if (isset($ptr[$prefix]))
	    {
	       $ptr =& $ptr[$prefix];
	    }
	    else
	    {
	       throw new Exception();
	    }
	 }
	 
	 return $ptr;
      }
      catch (Exception $e)
      {
	 $msg = 'failed for data: ' . var_export($data, true) .
	    ' with desired prefix: ' . var_export($prefix, true);
      
	 $this->em->notify('Log', array('Level'   => LOG_ERR,
					'Method'  => __METHOD__,
					'Message' => $msg));

	 throw new RuntimeException(__METHOD__ . ' ' . $msg);
      }
   }

   /// Get the events used for processing in the model.
   protected function getProcessingEventMap()
   {
      return array();
   }
   
   protected function offsetData($data)
   {
      return $this->offsetToPrefix($data, $this->setup['Data_Prefix']);
   }

   /*******************/
   /* Private Methods */
   /*******************/

   /** Offset the data to the specified prefix.
    *  @param data \array The data to offset.
    *  @param prefix \mixed The prefix to use.
    *  \return The data offset correctly.
    */
   private function offsetToPrefix($data, $prefix)
   {
      if (empty($prefix))
      {
	 return $data;
      }
      
      $offsetData = array();
      $offsetPtr =& $offsetData;
      
      if (is_array($prefix))
      {
	 foreach ($prefix as $p)
	 {
	    $offsetPtr[$p] = array();
	    $offsetPtr =& $offsetPtr[$p];
	 }
      }
      else
      {
	 $offsetPtr[$prefix] = array();
	 $offsetPtr =& $offsetPtr[$prefix];
      }

      $offsetPtr = $data;
      
      return $offsetData;
   }
}

// EOF