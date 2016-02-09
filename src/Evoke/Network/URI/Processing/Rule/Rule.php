<?php
/**
 * Rule
 *
 * @package   Evoke\Network\URI\Processing\Rule
 */
namespace Evoke\Network\URI\Processing\Rule;

/**
 * Rule
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\Network\URI\Processing\Rule
 */
abstract class Rule implements RuleIface
{
    /**
     * The callable used to execute the processing.
     * @var callable
     */
    protected $callable;

    /**
     * The data to process.
     * @var array
     */
    protected $data = [];

    /**
     * Rule constructor.
     *
     * @param $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
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
