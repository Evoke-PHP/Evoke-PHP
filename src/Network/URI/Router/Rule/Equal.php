<?php
declare(strict_types = 1);
/**
 * Equal Rule
 *
 * @package Evoke\Network\URI\Router\Rule
 */
namespace Evoke\Network\URI\Router\Rule;

/**
 * Equal Rule
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Evoke\Network\URI\Router\Rule
 */
class Equal extends Rule
{
    /**
     * The controller to use when the rule matches.
     *
     * @var string
     */
    protected $controller;

    /**
     * The URI to match.
     *
     * @var string
     */
    protected $match;

    /**
     * The parameters for the controller.
     *
     * @var mixed[]
     */
    protected $params;

    /**
     * Construct an Equal rule.
     *
     * @param string  $controller
     * @param string  $match
     * @param mixed[] $params
     * @param bool    $authoritative Whether the rule is authoritative.
     */
    public function __construct(string $controller, string $match, array $params = [], bool $authoritative = true)
    {
        parent::__construct($authoritative);

        $this->controller = $controller;
        $this->match      = $match;
        $this->params     = $params;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the controller.
     *
     * @return string The controller for the match.
     */
    public function getController() : string
    {
        return $this->controller;
    }

    /**
     * Get the parameters for the URI.
     *
     * @return mixed[] The controller parameters.
     */
    public function getParams() : array
    {
        return $this->params;
    }

    /**
     * Check the uri to see if it matches.
     *
     * @return bool Whether the uri is matched.
     */
    public function isMatch() : bool
    {
        return $this->uri == $this->match;
    }
}
// EOF
