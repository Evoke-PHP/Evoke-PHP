<?php
/**
 * Processing
 *
 * @package Processing
 */
namespace Evoke\Service;

/**
 * Processing
 *
 * The processing class handles the processing of data using callbacks.
 *
 * Each request is received as an array.  We match the keys of the request to
 * the callback array to determine the processing that should be done.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2013 Paul Young
 * @license   MIT
 * @package   Service
 */
class Processing implements ProcessingIface
{
	/**
	 * Associative array of request IDs to processing callback.
	 * @var mixed[]
	 */
	protected $callbacks;

	/**
	 * The data that we are processing.
	 * @var mixed[]
	 */
	protected $data;

	/**
	 * Whether a key is required to match for processing.
	 * @var bool
	 */
	protected $matchRequired;

	/**
	 * Whether only a single request type can be processed at a time.
	 * @var bool
	 */
	protected $uniqueMatch;

	/**
	 * Construct a Processing object.
	 *
	 * @param mixed[] Associative array of data to process.
	 * @param mixed[] Processing keys to callback lists.
	 * @param bool    Whether a match is required.
	 * @param bool    Whether a unique match is required.
	 */
	public function __construct(Array        $data,
	                            Array        $callbacks,
	                            /* Bool   */ $matchRequired = false,
	                            /* Bool   */ $uniqueMatch   = true)
	{
		$this->callbacks     = $callbacks;
		$this->data          = $data;
		$this->matchRequired = $matchRequired;
		$this->uniqueMatch   = $uniqueMatch;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Process the request.
	 */
	public function process()
	{
		$matchedCallbacks = $this->getMatchedCallbacks();
		
		if ($this->matchRequired && (count($matchedCallbacks) === 0))
		{
			trigger_error(
				'Match_Required for Request_Keys: ' .
				var_export(array_keys($this->requestKeys), true) .
				' with request data: ' . var_export($requestData, true),
				E_USER_WARNING);
			return;
		}
		elseif ($this->uniqueMatch && count($matchedCallbacks) > 1)
		{
			trigger_error(
				'Unique_Match required for Request_Keys: ' .
				var_export(array_keys($this->requestKeys)) .
				' with request data: ' . var_export($requestData, true),
				E_USER_WARNING);
			return;
		}

		foreach ($matchedCallbacks as $key => $callbacks)
		{
			// The key that defines the callback should not be passed.
			$callbackData = $this->data;
			unset($callbackData[$key]);
			
			if (is_callable($callbacks))
			{
				call_user_func($callbacks, $callbackData);
			}
			else
			{
				foreach ($callbacks as $callback)
				{
					call_user_func($callback, $callbackData);
				}
			}
		}
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	/**
	 * Get the matched callback lists for the data.
	 *
	 * @return mixed[] The callback lists that match the data.
	 */
	protected function getMatchedCallbacks()
	{
		return array_intersect_key($this->callbacks, $this->data);
	}
}
// EOF