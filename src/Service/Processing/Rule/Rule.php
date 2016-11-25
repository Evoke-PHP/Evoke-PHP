<?php
declare(strict_types = 1);
/**
 * Rule
 *
 * @package   Evoke\Service\Processing\Rule
 */
namespace Evoke\Service\Processing\Rule;

/**
 * Rule
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\Service\Processing\Rule
 */
abstract class Rule implements RuleIface
{
    /**
     * The callable used to execute the processing.
     * @var callable
     */
    protected $callback;

    /**
     * The data to process.
     * @var array
     */
    protected $data = [];

    /**
     * Rule constructor.
     *
     * @param $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * @inheritDoc
     */
    public function setData(Array $data)
    {
        $this->data = $data;
    }
}
// EOF
