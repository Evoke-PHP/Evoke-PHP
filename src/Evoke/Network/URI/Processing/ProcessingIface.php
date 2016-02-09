<?php
/**
 * Processing Interface
 *
 * @package Evoke\Network\URI\Processing
 */
namespace Evoke\Network\URI\Processing;

use DomainException;
use Evoke\Network\URI\Processing\Rule\RuleIface;

/**
 * Processing Interface
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Evoke\Network\URI\Processing
 */
interface ProcessingIface
{
    /**
     * Add a processing rule to the list of processing.
     *
     * @param RuleIface $rule The rule that is being added.
     */
    public function addRule(RuleIface $rule);

    /**
     * Process the data.
     *
     * @throws DomainException If the required match conditions aren't met.
     */
    public function process();

    /**
     * Set the data for the request that we are processing.
     *
     * @param mixed[] $data The request data that we are processing.
     */
    public function setData(Array $data);
}
// EOF
