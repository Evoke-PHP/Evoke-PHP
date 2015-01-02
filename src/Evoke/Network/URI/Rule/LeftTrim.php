<?php
/**
 * URI Left Trim Rule
 *
 * @package Network\URI\Rule
 */
namespace Evoke\Network\URI\Rule;

/**
 * URI Left Trim Rule
 *
 * A rule to trim characters from the left side of the URI.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Network\URI\Rule
 */
class LeftTrim extends Rule
{
    /**
     * Characters to trim from the left side of the URI.
     * @var string
     */
    protected $characters;

    /**
     * Construct the LeftTrim URI Rule.
     *
     * @param string $characters    The characters to left trim from the URI.
     * @param bool   $authoritative
     * Whether the rule can definitely give the final route for all URIs that it
     * matches.
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
        return ltrim($this->uri, $this->characters);
    }

    /**
     * Check the uri to see if it matches.
     *
     * @return bool Whether the uri is matched.
     */
    public function isMatch()
    {
        return isset($this->uri[0]) &&
            (strpos($this->characters, $this->uri[0]) !== false);
    }
}
// EOF
