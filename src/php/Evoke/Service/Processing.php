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
	 * @var callable[]
	 */
	protected $callbacks = array();

	/**
	 * The data that we are processing.
	 * @var mixed[]
	 */
	protected $data = array();

	/**
	 * Whether a key is required to match for processing.
	 * @var bool
	 */
	protected $matchRequired;

	/**
	 * Whether only a single request type can be processed at a time.
	 * @var bool
	 */
	protected $uniqueMatchRequired;

	/**
	 * Construct a Processing object.
	 *
	 * @param bool Whether a match is required.
	 * @param bool Whether a unique match is required.
	 */
	public function __construct(/* Bool */ $matchRequired       = false,
	                            /* Bool */ $uniqueMatchRequired = true)
	{
		$this->matchRequired       = $matchRequired;
		$this->uniqueMatchRequired = $uniqueMatchRequired;
	}

	/******************/
	/* Public Methods */
	/******************/

	public function appendCallback(/* String */ $processingKey,
	                               callable     $callback)
	{
		$this->callbacks[$processingKey][] = $callback;
	}
	
	/**
	 * Process the request.
	 */
	public function process()
	{
		$matchedKeys = array_intersect_key($this->callbacks, $this->data);
	
		if ($this->matchRequired && (count($matchedKeys) === 0))
		{
			throw new DomainException(
				'Match_Required processing request with keys: ' .
				implode(' ', array_keys($this->data)) .
				' recognized keys are: ' .
				implode(' ', array_keys($this->callbacks)));
		}
		elseif ($this->uniqueMatchRequired && count($matchedKeys) > 1)
		{
			throw new DomainException(
				'Unique match required processing request with keys: ' .
				implode(' ', array_keys($this->data)) .
				' recognized keys are: ' .
				implode(' ', array_keys($this->callbacks)),
				' matched keys are: ' .
				implode(' ', $matchedKeys));
		}

		foreach ($matchedKeys as $key => $callbacks)
        {
            // The key that defines the callback should not be passed.
            $callbackData = $this->data;
            unset($callbackData[$key]);

            foreach ($callbacks as $callback)
            {
	            call_user_func($callback, $callbackData);
            }
        }
	}

	/**
	 * Set the data for the request that we are processing.
	 *
	 * @param mixed[] The request data that we are processing.
	 */
	public function setData(Array $data)
	{
		$this->data = $data;
	}
}
// EOF