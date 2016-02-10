<?php
/**
 * KeyOtherValue
 *
 * @package   Evoke\Service\Processing\Rule
 */
namespace Evoke\Service\Processing\Rule;

/**
 * KeyOtherValue
 *
 * {@inheritdoc} The callback is called with the value of the other key.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\Service\Processing\Rule
 */
class KeyOtherValue extends Key
{
    /**
     * The Other Key to use to get the value.
     * @var string
     */
    protected $otherKey;

    /**
     * KeyOtherValue constructor.
     *
     * @param callable $callback
     * @param string   $key
     * @param string   $otherKey
     */
    public function __construct(callable $callback, $key, $otherKey)
    {
        parent::__construct($callback, $key);

        $this->otherKey = $otherKey;
    }


    /**
     * @inheritDoc
     */
    public function execute()
    {
        call_user_func($this->callback, $this->data[$this->otherKey]);
    }

    /**
     * @inheritDoc
     */
    public function isMatch()
    {
        return isset($this->data[$this->key], $this->data[$this->otherKey]);
    }
}
// EOF
