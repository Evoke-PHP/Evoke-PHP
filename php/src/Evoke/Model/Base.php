<?php
namespace Evoke\Model;
/// Provides the basic implementation for a model.
abstract class Base implements \Evoke\Core\Iface\Model
{
	/** @property $EventManager
	 *  EventManager obect.
	 */
	protected $EventManager;

	/** @property $dataPrefix
	 *  \mixed String, array or NULL, specifying the prefix to offset the
	 *  retrieved data with.
	 */
	protected $dataPrefix;	
	
	/** @property $gotDataEvent
	 *  \string Event name to call when we have retrieved data.
	 */
	protected $gotDataEvent;
	
	public function __construct(Array $setup)
	{
		$setup += array('Data_Prefix'    => NULL, // Can be NULL for no prefix.
		                'Event_Manager'   => NULL,
		                'Got_Data_Event' => 'Model.Got_Data');

		if (!$setup['Event_Manager'] instanceof \Evoke\Core\EventManager)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Event_Manager');
		}

		$this->EventManager = $setup['Event_Manager'];
		$this->dataPrefix   = $setup['Data_Prefix'];
		$this->gotDataEvent = $setup['Got_Data_Event'];
	}
   
	/******************/
	/* Public Methods */
	/******************/
   
	/// Get the data for the model.
	public function getData()
	{
		return $this->offsetData(array());
	}
   
	/// Notify the data that the model has.
	public function notifyData()
	{
		$this->EventManager->notify($this->gotDataEvent, $this->getData());
	}
   
	/*********************/
	/* Protected Methods */
	/*********************/

	/** Get a subset of the data at the specified prefix.
	 *  @param data \array The data to retrieve a part from.
	 *  @param prefix \mixed The offset in the data to return data from.
	 */
	protected function getAtPrefix(Array $data, $prefix)
	{
		if (empty($prefix))
		{
			return $data;
		}
      
		if (!is_array($prefix))
		{
			$prefix = array($prefix);
		}

		$ptr =& $data;

		foreach ($prefix as $offset)
		{
			if (!isset($ptr[$offset]))
			{
				$msg = 'failed for data: ' . var_export($data, true) .
					' with desired prefix: ' . var_export($prefix, true) .
					' at offset: ' . var_export($offset, true);
				
				$this->EventManager->notify('Log', array('Level'   => LOG_ERR,
				                                         'Message' => $msg,
				                                         'Method'  => __METHOD__));

				throw new \RuntimeException(__METHOD__ . ' ' . $msg);
			}
			
			$ptr =& $ptr[$offset];
		}

		return $ptr;
	}

	/** Offset the data to the \ref dataPrefix
	 *  @param data \array The data to be offset.
	 */
	protected function offsetData(Array $data)
	{
		return $this->offsetToPrefix($data, $this->dataPrefix);
	}

	/*******************/
	/* Private Methods */
	/*******************/

	/** Offset the data to the specified prefix.
	 *  @param data \array The data to offset.
	 *  @param prefix \mixed The prefix to use.
	 *  \return The data offset correctly.
	 */
	private function offsetToPrefix(Array $data, $prefix)
	{
		if (empty($prefix))
		{
			return $data;
		}
      
		$offsetData = array();
		$offsetPtr =& $offsetData;
      
		if (!is_array($prefix))
		{
			$prefix = array($prefix);
		}
		
		foreach ($prefix as $offset)
		{
			$offsetPtr[$offset] = array();
			$offsetPtr =& $offsetPtr[$offset];
		}

		$offsetPtr = $data;
      
		return $offsetData;
	}
}
// EOF