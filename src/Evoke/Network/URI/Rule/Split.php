<?php
/**
 * Split
 *
 * @package Network\URI\Rule
 */
namespace Evoke\Network\URI\Rule;

use InvalidArgumentException;

/**
 * URI rule to split the uri into named parameters after an optional prefix.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Network\URI\Rule
 */
class Split extends Rule
{
    /**
     * Controller
     * @var string
     */
    protected $controller;

    /**
     * Parts
     * @var string[]
     */
    protected $parts;

    /**
     * Prefix string to ignore for breakdown into parts. It must match the start of the URI for the rule to match.
     * @var string
     */
    protected $prefix;

    /**
     * Pre-calculated length for usage throughout.
     * @var int
     */
    protected $prefixLen;

    /**
     * Separator
     * @var string
     */
    protected $separator;

    /**
     * Construct a Split object to split the uri into named parameters after an optional prefix.
     *
     * @param string   $controller
     * @param string[] $parts
     * @param string   $prefix        The prefix to match.
     * @param string   $separator     Separator to use to split the parts.
     * @param bool     $authoritative Whether the rule is authoritative.
     * @throws InvalidArgumentException
     */
    public function __construct($controller, Array $parts, $prefix, $separator, $authoritative = true)
    {
        parent::__construct($authoritative);

        if (empty($parts)) {
            throw new InvalidArgumentException('need parts as non-empty array.');
        }

        if (empty($separator)) {
            throw new InvalidArgumentException('need separator as non-empty string.');
        }

        $this->controller = $controller;
        $this->parts      = $parts;
        $this->prefix     = $prefix;
        $this->prefixLen  = strlen($prefix);
        $this->separator  = $separator;
    }

    /**
     * Get the controller.
     *
     * @return string The controller.
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Return the parameters for the URI.
     *
     * @return mixed[] The parameters found using the rule.
     */
    public function getParams()
    {
        return array_combine(
            $this->parts,
            explode($this->separator, substr($this->uri, $this->prefixLen))
        );
    }

    /**
     * Check the uri to see if it matches.
     *
     * @return bool Whether the uri is matched.
     */
    public function isMatch()
    {
        // The prefix matches AND we have the expected number of parts.
        return strcmp($this->prefix, substr($this->uri, 0, $this->prefixLen)) === 0 &&
        (count(explode($this->separator, substr($this->uri, $this->prefixLen))) === count($this->parts));
    }
}
// EOF
