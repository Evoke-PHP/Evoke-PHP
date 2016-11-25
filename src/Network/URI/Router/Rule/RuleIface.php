<?php
/**
 * URI Rule Interface
 *
 * @package Evoke\Network\URI\Router\Rule
 */
namespace Evoke\Network\URI\Router\Rule;

/**
 * URI Rule Interface
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Evoke\Network\URI\Router\Rule
 */
interface RuleIface
{
    /**
     * Get the controller.
     *
     * @return string The uri mapped towards the controller with the rule.
     */
    public function getController();

    /**
     * Return the parameters for the URI.
     *
     * @return mixed[] The parameters found using the rule.
     */
    public function getParams();

    /**
     * Check whether the rule is authoritative.
     *
     * @return bool Whether the rule can definitely give the final route when it matches the input.
     */
    public function isAuthoritative();

    /**
     * Check to see if the rule matches.
     *
     * @return bool Whether the rule matches.
     */
    public function isMatch();

    /**
     * Set the URI that the rule is acting upon.
     *
     * @param string $uri The URI that the rule should act upon.
     */
    public function setURI($uri);
}
// EOF
