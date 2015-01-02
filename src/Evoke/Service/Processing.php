<?php
/**
 * Processing
 *
 * @package Processing
 */
namespace Evoke\Service;

use DomainException;

/**
 * Processing
 *
 * The processing class handles the processing of data using callbacks.
 *
 * Each request is received as an array.  We match the keys of the request to
 * the callback array to determine the processing that should be done.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Service
 */
class Processing implements ProcessingIface
{
    /**
     * Associative array of request IDs to processing callback.
     *
     * @var callable[]
     */
    protected $callbacks = [];

    /**
     * The data that we are processing.
     *
     * @var mixed[]
     */
    protected $data = [];

    /**
     * Whether a key is required to match for processing.
     *
     * @var bool
     */
    protected $matchRequired = false;

    /**
     * Whether only a single request type can be processed at a time.
     *
     * @var bool
     */
    protected $uniqueMatchRequired = true;

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Add a callback to the request key's list of callbacks.
     *
     * @param string   $processingKey The request key for the matching.
     * @param callable $callback      The callback that is being added.
     */
    public function addCallback(
        $processingKey,
        callable     $callback
    ) {
        if (!isset($this->callbacks[$processingKey])) {
            $this->callbacks[$processingKey] = [];
        }

        $this->callbacks[$processingKey][] = $callback;
    }

    /**
     * Process the request.
     *
     * @throws DomainException If the required match conditions aren't met.
     */
    public function process()
    {
        if (empty($this->data)) {
            $matchedKeys = empty($this->callbacks['']) ?
                [] : ['' => $this->callbacks['']];
        } else {
            $matchedKeys = array_intersect_key($this->callbacks, $this->data);
        }

        if ($this->matchRequired && (count($matchedKeys) === 0)) {
            throw new DomainException(
                'Match required processing request with keys: ' .
                implode(', ', array_keys($this->data)) .
                ' recognized keys are: ' .
                implode(' ', array_keys($this->callbacks)));
        } elseif ($this->uniqueMatchRequired && count($matchedKeys) > 1) {
            throw new DomainException(
                'Unique match required processing request with keys: ' .
                implode(', ', array_keys($this->data)) .
                ' recognized keys are: ' .
                implode(', ', array_keys($this->callbacks)) .
                ' matched keys are: ' .
                implode(', ', array_keys($matchedKeys)));
        }

        foreach ($matchedKeys as $key => $callbacks) {
            // The key that defines the callback should not be passed.
            $callbackData = $this->data;
            unset($callbackData[$key]);

            foreach ($callbacks as $callback) {
                call_user_func($callback, $callbackData);
            }
        }
    }

    /**
     * Set the data for the request that we are processing.
     *
     * @param mixed[] $data The request data that we are processing.
     */
    public function setData(Array $data)
    {
        $this->data = $data;
    }

    /**
     * Set whether a match is required when processing the data.
     *
     * @param bool $matchRequired Whether a match is required.
     */
    public function setMatchRequired($matchRequired = true)
    {
        $this->matchRequired = $matchRequired;
    }

    /**
     * Set whether a unique match is required when processing the data.
     *
     * @param bool $uniqueMatchRequired Whether a unique match is required.
     */
    public function setUniqueMatchRequired($uniqueMatchRequired = true)
    {
        $this->uniqueMatchRequired = $uniqueMatchRequired;
    }
}
// EOF
