<?php
/**
 * RuleIface
 *
 * @package   Evoke\Network\URI\Processing\Rule
 */
namespace Evoke\Network\URI\Processing\Rule;

/**
 * RuleIface
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\Network\URI\Processing\Rule
 */
interface RuleIface
{
    /**
     * Execute the processing for a matched rule.
     */
    public function execute();

    /**
     * Return whether the rule matches the data.
     *
     * @return bool
     */
    public function isMatch();

    /**
     * Set the data for the rule to work on.
     *
     * @param array $data
     */
    public function setData(Array $data);
}
// EOF
