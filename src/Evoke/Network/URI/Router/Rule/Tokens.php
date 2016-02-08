<?php
/**
 * HTTP URI Tokens rule
 *
 * @package   Evoke\Network\URI\Router\Rule
 */
namespace Evoke\Network\URI\Router\Rule;

use InvalidArgumentException;

/**
 * HTTP URI Tokens rule
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\Network\URI\Router\Rule
 */
class Tokens extends Rule
{
    /**
     * Controller
     * @var string
     */
    protected $controller;

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
     * Token characters to split the parameters with.
     * @var string
     */
    protected $tokenCharacters;

    /**
     * Construct a tokens rule to split the uri into parameters after an optional prefix.
     *
     * @param string   $controller
     * @param string   $prefix          The prefix to match.
     * @param string   $tokenCharacters Separator to use to split the parts.
     * @param bool     $authoritative   Whether the rule is authoritative.
     * @throws InvalidArgumentException
     */
    public function __construct($controller, $prefix, $tokenCharacters = '/', $authoritative = true)
    {
        parent::__construct($authoritative);

        if (empty($tokenCharacters)) {
            throw new InvalidArgumentException('need token characters as non-empty string.');
        }

        $this->controller      = $controller;
        $this->prefix          = $prefix;
        $this->prefixLen       = strlen($prefix);
        $this->tokenCharacters = $tokenCharacters;
    }

    /******************/
    /* Public Methods */
    /******************/

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
        $token = strtok(substr($this->uri, $this->prefixLen), $this->tokenCharacters);

        if (!$token) {
            return [];
        }

        $tokens = [$token];

        while ($token = strtok($this->tokenCharacters)) {
            $tokens[] = $token;
        }

        return $tokens;
    }

    /**
     * @inheritDoc
     */
    public function isMatch()
    {
        return substr($this->uri, 0, $this->prefixLen) === $this->prefix;
    }
}
// EOF
