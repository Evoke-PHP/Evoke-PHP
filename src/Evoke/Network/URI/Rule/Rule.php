<?php
/**
 * URI Rule Interface
 *
 * @package Network\URI\Rule
 */
namespace Evoke\Network\URI\Rule;

use InvalidArgumentException;

/**
 * URI Rule Interface
 *
 * Map the URI to a controller and parameters.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Network\URI\Rule
 */
abstract class Rule implements RuleIface
{
    /**
     * Whether the rule can definitely give the final route for all URIs that
     * it matches.
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
     * @param bool Whether the rule can definitely give the final route for all
     *             URIs that it matches.
     */
    public function __construct(/* Bool */ $authoritative)
    {
        $this->authoritative = $authoritative;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the parameters for the URI.
     *
     * @return [] Empty Array. (By default no parameters are captured)
     */
    public function getParams()
    {
        return array();
    }

    /**
     * Check whether the rule is authoritative.
     *
     * @return bool Whether the rule can definitely give the final route for all
     *              URIs that it matches.
     */
    public function isAuthoritative()
    {
        return $this->authoritative;
    }

    /**
     * Set the URI that the rule is acting upon.
     *
     * @param string The value to set the URI to.
     */
    public function setURI($uri)
    {
        if (!is_string($uri))
        {
            throw new InvalidArgumentException('needs URI as string.');
        }

        $this->uri = $uri;
    }
}
// EOF