<?php
/**
 * URI Regex Rule with one match used for all calculations.
 *
 * @package Evoke\Network\URI\Router\Rule
 */
namespace Evoke\Network\URI\Router\Rule;

use InvalidArgumentException;

/**
 * URI Regex Rule with one match used for all calculations.
 *
 * A regex rule to map the uri controller and parameters.  There is a single match for the URI, with all replacements
 * being made from this match.  If there are more complex requirements such as optional parameters then the
 * RegexTwoLevel rule should be used.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Evoke\Network\URI\Router\Rule
 */
class RegexSharedMatch extends Rule
{
    /**
     * Regex match for the URI.
     *
     * @var string
     */
    protected $match;

    /**
     * Parameter key and value replacement regex for the URI.
     *
     * @var Array[]
     */
    protected $params;

    /**
     * Regex replacement for the controller.
     *
     * @var string
     */
    protected $replacement;

    /**
     * Construct the Regex Rule.
     *
     * @param string  $match         The Regex to match the URI with.
     * @param string  $replacement   The controller regex replacement string.
     * @param Array[] $params        Regex replacements for the parameters.
     * @param bool    $authoritative Is this always the final route?
     * @throws InvalidArgumentException If the rule is incorrectly formatted.
     */
    public function __construct(
        $match,
        $replacement,
        Array        $params = [],
        $authoritative = false
    ) {
        parent::__construct($authoritative);

        foreach ($params as $index => $paramSpec) {
            if (!isset($paramSpec['key'], $paramSpec['value'])) {
                throw new InvalidArgumentException('param spec needs key and value at index: ' . $index);
            }
        }

        $this->match       = $match;
        $this->params      = $params;
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
    public function getController()
    {
        return preg_replace($this->match, $this->replacement, $this->uri);
    }

    /**
     * Get any parameters.
     *
     * @return mixed[] Parameters from the URI.
     */
    public function getParams()
    {
        $params = [];

        foreach ($this->params as $paramSpec) {
            $params[preg_replace($this->match, $paramSpec['key'], $this->uri)] =
                preg_replace($this->match, $paramSpec['value'], $this->uri);
        }

        return $params;
    }

    /**
     * Check the uri to see if it matches.
     *
     * @return bool Whether the uri is matched.
     */
    public function isMatch()
    {
        $result = preg_match($this->match, $this->uri);

        return $result !== false && $result > 0;
    }
}
// EOF
