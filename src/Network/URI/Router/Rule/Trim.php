<?php
declare(strict_types = 1);
/**
 * URI Trim Rule
 *
 * @package Evoke\Network\URI\Router\Rule
 */
namespace Evoke\Network\URI\Router\Rule;

/**
 * URI Trim Rule
 *
 * A rule to trim characters from the URI.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Evoke\Network\URI\Router\Rule
 */
class Trim extends Rule
{
    /**
     * Characters to trim from the URI.
     *
     * @var string
     */
    protected $characters;

    /**
     * Construct the Trim Rule.
     *
     * @param string $characters    The characters to trim from the URI.
     * @param bool   $authoritative Whether the rule can definitely give the final route for all URIs that it matches.
     */
    public function __construct($characters, $authoritative = false)
    {
        parent::__construct($authoritative);

        $this->characters = $characters;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the controller.
     *
     * @return string The uri trimmed appropriately.
     */
    public function getController()
    {
        return trim($this->uri, $this->characters);
    }

    /**
     * Check the uri to see if it matches.
     *
     * @return bool Whether the uri is matched.
     */
    public function isMatch()
    {
        return trim($this->uri, $this->characters) !== $this->uri;
    }
}
// EOF
