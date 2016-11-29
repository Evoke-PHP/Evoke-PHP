<?php
declare(strict_types = 1);
/**
 * URI Rule Interface
 *
 * @package Evoke\Network\URI\Router\Rule
 */
namespace Evoke\Network\URI\Router\Rule;

use InvalidArgumentException;

/**
 * URI Rule Interface
 *
 * Map the URI to a controller and parameters.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Evoke\Network\URI\Router\Rule
 */
abstract class Rule implements RuleIface
{
    /**
     * Whether the rule can definitely give the final route for all URIs that it matches.
     * @var bool
     */
    protected $authoritative;

    /**
     * The URI that the rule is acting upon.
     * @var string
     */
    protected $uri;

    /**
     * Construct the URI Rule.
     *
     * @param bool $authoritative Whether the rule can definitely give the final route for all URIs that it matches.
     */
    public function __construct(bool $authoritative)
    {
        $this->authoritative = $authoritative;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the parameters for the URI.
     *
     * @return array Empty Array. (By default no parameters are captured)
     */
    public function getParams() : array
    {
        return [];
    }

    /**
     * Check whether the rule is authoritative.
     *
     * @return bool Whether the rule can definitely give the final route for all URIs that it matches.
     */
    public function isAuthoritative() : bool
    {
        return $this->authoritative;
    }

    /**
     * Set the URI that the rule is acting upon.
     *
     * @param string $uri The value to set the URI to.
     */
    public function setURI(string $uri)
    {
        $this->uri = $uri;
    }
}
// EOF
