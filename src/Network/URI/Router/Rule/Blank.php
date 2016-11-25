<?php
declare(strict_types = 1);
/**
 * Blank Rule
 *
 * @package Evoke\Network\URI\Router\Rule
 */
namespace Evoke\Network\URI\Router\Rule;

/**
 * Blank Rule
 *
 * A rule to match blank URIs.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Evoke\Network\URI\Router\Rule
 */
class Blank extends Rule
{
    /**
     * Replacement for a blank URI.
     *
     * @var string
     */
    protected $replacement;

    /**
     * Construct the Blank URI Rule.
     *
     * @param string $replacement   Replacement for a blank URI.
     * @param bool   $authoritative Whether the rule can definitely give the final route for all URIs that it matches.
     */
    public function __construct($replacement, $authoritative = true)
    {
        parent::__construct($authoritative);

        $this->replacement = $replacement;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the controller.
     *
     * @return string The uri for the blank controller.
     */
    public function getController()
    {
        return $this->replacement;
    }

    /**
     * Check the uri to see if it matches.
     *
     * @return bool Whether the uri is matched.
     */
    public function isMatch()
    {
        return empty($this->uri);
    }
}
// EOF
