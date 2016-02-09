<?php
/**
 * KeyOther
 *
 * @package   Evoke\Service\Processing\Rule
 */

namespace Evoke\Service\Processing\Rule;

/**
 * KeyOther
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\Service\Processing\Rule
 */
class KeyOther extends Rule
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
        $data = $this->data;
        unset($data[$this->key]);

        call_user_func($this->callable, $data);
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
