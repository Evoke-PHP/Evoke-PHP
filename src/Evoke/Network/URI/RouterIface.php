<?php
/**
 * URI Router Interface
 *
 * @package Network\URI
 */
namespace Evoke\Network\URI;

/**
 * URI Router Interface
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Network\URI
 */
interface RouterIface
{
    /**
     * Add a rule to the router.
     *
     * @param Rule\RuleIface $rule
     */
    public function addRule(Rule\RuleIface $rule);

    /**
     * Perform the routing based on the added rules.
     *
     * @param string $uri The URI to route.
     */
    public function route($uri);
}
// EOF
