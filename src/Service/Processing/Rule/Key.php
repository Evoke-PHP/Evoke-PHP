<?php
declare(strict_types = 1);
/**
 * Key
 *
 * @package   Evoke\Service\Processing\Rule
 */
namespace Evoke\Service\Processing\Rule;

/**
 * Key rules match on a single key in the data.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\Service\Processing\Rule
 */
abstract class Key extends Rule
{
    /**
     * The key in the data to match on.
     * @var string
     */
    protected $key;

    /**
     * KeyValue constructor.
     *
     * @param callable $callback
     * @param string   $key
     */
    public function __construct(callable $callback, $key)
    {
        parent::__construct($callback);

        $this->key = $key;
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
