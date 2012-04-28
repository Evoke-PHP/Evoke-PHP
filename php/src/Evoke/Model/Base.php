<?php
namespace Evoke\Model;
/// Provides the basic implementation for a model.
abstract class Base implements \Evoke\Iface\Model
{
	/** @property $dataPrefix
	 *  \array specifying the prefix to offset the retrieved data with.
	 */
	protected $dataPrefix;

	/** Construct the basic functions of a model.
	 *  @param dataPrefix \array Models return data at the specified prefix.
	 *  This allows data to be combined between models while allowing for
	 *  disambiguation based on the offset given to each model.  By default
	 *  there is no offset and the data is returned as is.
	 */
	public function __construct(Array $dataPrefix = array())
	{
		$this->dataPrefix = $dataPrefix;
	}
   
	/******************/
	/* Public Methods */
	/******************/
   
	/** Get the data for the model.
	 *  @return \array The data (by default an empty array).
	 */
	public function getData()
	{
		return $this->offsetData(array());
	}
   
	/*********************/
	/* Protected Methods */
	/*********************/

	/** Get a subset of the data at the specified prefix.
	 *  @param data \array The data to retrieve a part from.
	 *  @param prefix \mixed The offset in the data to return data from.
	 */
	protected function getAtPrefix(Array $data, Array $prefix)
	{
		if (empty($prefix))
		{
			return $data;
		}

		$ptr =& $data;

		foreach ($prefix as $offset)
		{
			if (!isset($ptr[$offset]))
			{
				$msg = 'failed for data: ' . var_export($data, true) .
					' with desired prefix: ' . var_export($prefix, true) .
					' at offset: ' . var_export($offset, true);
				
				$this->eventManager->notify('Log', array('Level'   => LOG_ERR,
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
	 *  @return The data offset correctly.
	 */
	protected function offsetData($data)
	{
		$offsetData = array();
		$offsetPtr =& $offsetData;
		
		foreach ($this->prefix as $offset)
		{
			$offsetPtr[$offset] = array();
			$offsetPtr =& $offsetPtr[$offset];
		}

		// We have got the correct offset, now set the data at that offset.
		$offsetPtr = $data;
      
		return $offsetData;
	}
}
// EOF