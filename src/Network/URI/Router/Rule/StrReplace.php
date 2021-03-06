<?php
declare(strict_types = 1);
/**
 * URI String Replace Rule
 *
 * @package Evoke\Network\URI\Router\Rule
 */
namespace Evoke\Network\URI\Router\Rule;

/**
 * URI String Replace Rule
 *
 * A rule to change strings from the URI so that a controller can be formed. No parameters are matched by this class.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Evoke\Network\URI\Router\Rule
 */
class StrReplace extends Rule
{
    /**
     * The string to match on.
     * @var string
     */
    protected $match;

    /**
     * The string to use as a replacement.
     * @var string
     */
    protected $replacement;

    /**
     * Construct the string replacements rule.
     *
     * @param string $match         The string to match on.
     * @param string $replacement   The string to use as a replacement.
     * @param bool   $authoritative Whether the rule can definitely give the final route for all URIs that it matches.
     */
    public function __construct(string $match, string $replacement, bool $authoritative = false)
    {
        parent::__construct($authoritative);

        $this->match       = $match;
        $this->replacement = $replacement;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the controller.
     *
     * @return string The uri with the string replacements made.
     */
    public function getController() : string
    {
        return str_replace($this->match, $this->replacement, $this->uri);
    }

    /**
     * Check the uri to see if it matches.
     *
     * @return bool Whether the uri is matched.
     */
    public function isMatch() : bool
    {
        return strpos($this->uri, $this->match) !== false;
    }
}
// EOF
