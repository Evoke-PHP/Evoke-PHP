<?php
declare(strict_types = 1);
/**
 * URI Regex Named Rule
 *
 * @package Evoke\Network\URI\Router\Rule
 */
namespace Evoke\Network\URI\Router\Rule;

/**
 * URI Regex Named Rule
 *
 * A regex rule to map the uri controller and parameters.  There is a single match for the URI, with all replacements
 * being made from this match.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Evoke\Network\URI\Router\Rule
 */
class RegexNamed extends Rule
{
    /**
     * Regex match for the URI.  The named subpatterns are used for the parameters.
     *
     * @var string
     */
    protected $match;

    /**
     * Regex replacement for the controller.  Any named subpatterns must be referred to by number in the replacement.
     *
     * @var string
     */
    protected $replacement;

    /**
     * Construct the Regex Named rule.
     *
     * @param string $match         Regex to match the URI with named subpatterns.
     * @param string $replacement   The controller regex replacement string.
     * @param bool   $authoritative Is this always the final route?
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
     * @return string The uri with the match replaced.
     */
    public function getController() : string
    {
        return preg_replace($this->match, $this->replacement, $this->uri);
    }

    /**
     * Get any parameters.
     *
     * @return mixed[] Named parameters from the URI subpattern matches.
     */
    public function getParams() : array
    {
        preg_match($this->match, $this->uri, $params);

        // Return only the named parameters rather than the numbered ones.
        foreach (array_keys($params) as $key) {
            if (!is_string($key)) {
                unset($params[$key]);
            }
        }

        return $params;
    }

    /**
     * Check the uri to see if it matches.
     *
     * @return bool Whether the uri is matched.
     */
    public function isMatch() : bool
    {
        $result = preg_match($this->match, $this->uri);

        return $result !== false && $result > 0;
    }
}
// EOF
