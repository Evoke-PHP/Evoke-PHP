<?php
declare(strict_types = 1);
/**
 * Processing
 *
 * @package Evoke\Service\Processing
 */
namespace Evoke\Service\Processing;

use DomainException;
use Evoke\Service\Processing\Rule\RuleIface;

/**
 * Processing
 *
 * The processing class handles the processing of data using callbacks.
 *
 * Each request is received as an array.  We match the keys of the request to the callback array to determine the
 * processing that should be done.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Evoke\Service\Processing
 */
class Processing implements ProcessingIface
{
    /**
     * The data that we are processing.
     * @var mixed[]
     */
    protected $data = [];

    /**
     * Whether a key is required to match for processing.
     * @var bool
     */
    protected $matchRequired = false;

    /**
     * The rules to use for processing the data.
     * @var RuleIface[]
     */
    protected $rules = [];

    /**
     * Whether only a single rule is allowed to match for processing.
     * @var bool
     */
    protected $uniqueMatchRequired = false;

    /******************/
    /* Public Methods */
    /******************/

    /**
     * @inheritDoc
     */
    public function addRule(RuleIface $rule)
    {
        $this->rules[] = $rule;
    }

    /**
     * @inheritDoc
     */
    public function process()
    {
        $matchedRules = [];

        foreach ($this->rules as $rule) {
            $rule->setData($this->data);

            if ($rule->isMatch()) {
                $matchedRules[] = $rule;
            }
        }

        if ($this->matchRequired && empty($matchedRules)) {
            throw new DomainException('Match required while processing: ' . var_export($this->data, true));
        } elseif ($this->uniqueMatchRequired && count($matchedRules) > 1) {
            throw new DomainException('Unique match required while processing: ' . var_export($this->data, true));
        }

        foreach ($matchedRules as $matchedRule) {
            $matchedRule->execute();
        }
    }

    /**
     * @inheritDoc
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * Set whether a match is required when processing the data.
     *
     * @param bool $matchRequired Whether a match is required.
     */
    public function setMatchRequired(bool $matchRequired = true)
    {
        $this->matchRequired = $matchRequired;
    }

    /**
     * Set whether a unique match is required when processing the data.
     *
     * @param bool $uniqueMatchRequired Whether a unique match is required.
     */
    public function setUniqueMatchRequired(bool $uniqueMatchRequired = true)
    {
        $this->uniqueMatchRequired = $uniqueMatchRequired;
    }
}
// EOF
