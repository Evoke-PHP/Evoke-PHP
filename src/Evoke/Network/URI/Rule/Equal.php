<?php
/**
 * Equal Rule
 *
 * @package Network\URI\Rule
 */
namespace Evoke\Network\URI\Rule;

/**
 * Equal Rule
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Network\URI\Rule
 */
class Equal extends Rule
{
    protected
        /**
         * The controller to use when the rule matches.
         * @var string
         */
        $controller,

        /**
         * The URI to match.
         * @var string
         */
        $match,

        /**
         * The parameters for the controller.
         * @var mixed[]
         */
        $params;

    /**
     * Construct an Equal rule.
     *
     * @param string  Controller.
     * @param string  Match.
     * @param mixed[] Params.
     * @param bool    Whether the rule is authoritative.
     */
    public function __construct(/* string */ $controller,
                                /* string */ $match,
                                Array        $params        = [],
                                /* bool   */ $authoritative = true)
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
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Get the parameters for the URI.
     *
     * @return mixed[] The controller parameters.
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /**
     * Check the uri to see if it matches.
     *
     * @return bool Whether the uri is matched.
     */
    public function isMatch()
    {
        return $this->uri == $this->match;
    }
}
// EOF