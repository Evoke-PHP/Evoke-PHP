<?php
declare(strict_types = 1);
/**
 * URI Router Interface
 *
 * @package Evoke\Network\URI\Router
 */
namespace Evoke\Network\URI\Router;

/**
 * URI Router Interface
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Evoke\Network\URI\Router
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
     * @return mixed[] The details for the route.
     */
    public function route(string $uri) : array;
}
// EOF
