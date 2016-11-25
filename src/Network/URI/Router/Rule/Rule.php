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
    public function __construct($authoritative)
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
    public function getParams()
    {
        return [];
    }

    /**
     * Check whether the rule is authoritative.
     *
     * @return bool Whether the rule can definitely give the final route for all URIs that it matches.
     */
    public function isAuthoritative()
    {
        return $this->authoritative;
    }

    /**
     * Set the URI that the rule is acting upon.
     *
     * @param string $uri The value to set the URI to.
     */
    public function setURI($uri)
    {
        if (!is_string($uri)) {
            throw new InvalidArgumentException('needs URI as string.');
        }

        $this->uri = $uri;
    }
}
// EOF
