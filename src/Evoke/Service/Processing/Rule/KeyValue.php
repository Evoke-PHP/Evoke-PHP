<?php
/**
 * KeyValue
 *
 * @package   Evoke\Service\Processing\Rule
 */

namespace Evoke\Service\Processing\Rule;

/**
 * KeyValue
 *
 * The KeyValue rule matches if the key is present within the data.  The callback is called with the value of the key.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\Service\Processing\Rule
 */
class KeyValue extends Rule
{
    /**
     * The key in the data to use for the value passed to the callback.
     * @var string
     */
    protected $key;

    /**
     * KeyValue constructor.
     *
     * @param callable $callable
     * @param string   $key
     */
    public function __construct(callable $callable, $key)
    {
        parent::__construct($callable);

        $this->key = $key;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * @inheritDoc
     */
    public function execute()
    {
        call_user_func($this->callable, $this->data[$this->key]);
    }

    /**
     * @inheritDoc
     */
    public function isMatch()
    {
        return isset($this->data[$this->key]);
    }
}
// EOF
