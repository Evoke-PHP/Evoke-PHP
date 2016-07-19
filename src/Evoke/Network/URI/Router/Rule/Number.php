<?php
/**
 * Number
 *
 * @package   Evoke\Network\URI\Router\Rule
 */
namespace Evoke\Network\URI\Router\Rule;

/**
 * Number
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\Network\URI\Router\Rule
 */
class Number extends Rule
{
    /**
     * Controller
     * @var string
     */
    protected $controller;

    /**
     * The parameter key to store the number in.
     * @var string
     */
    protected $key;

    /**
     * Prefix string to match before the numbered part of the URI.
     * @var string
     */
    protected $prefix;

    /**
     * Pre-calculated length for usage throughout.
     * @var int
     */
    protected $prefixLen;

    /**
     * Construct a Split object to split the uri into named parameters after an optional prefix.
     *
     * @param string   $controller
     * @param string   $key           The parameter key to store the number in.
     * @param string   $prefix        The prefix to match.
     * @param bool     $authoritative Whether the rule is authoritative.
     * @throws InvalidArgumentException
     */
    public function __construct($controller, $key, $prefix, $authoritative = true)
    {
        parent::__construct($authoritative);

        $this->controller = $controller;
        $this->key        = $key;
        $this->prefix     = $prefix;
        $this->prefixLen  = strlen($prefix);
    }

    /**
     * @inheritDoc
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @inheritDoc
     */
    public function getParams()
    {
        return [$this->key => (int)(substr($this->uri, $this->prefixLen))];
    }

    /**
     * @inheritDoc
     */
    public function isMatch()
    {
        return (
            strcmp($this->prefix, substr($this->uri, 0, $this->prefixLen)) === 0 &&
            ctype_digit(substr($this->uri, $this->prefixLen))
        );
    }
}
// EOF
