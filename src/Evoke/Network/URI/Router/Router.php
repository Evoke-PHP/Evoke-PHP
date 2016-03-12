<?php
/**
 * URI Router Interface
 *
 * @package Evoke\Network\URI\Router
 */
namespace Evoke\Network\URI\Router;

/**
 * URI Router Interface
 *
 * Route the Request to a controller and parameters.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Evoke\Network\URI\Router
 */
class Router implements RouterIface
{
    /**
     * Rules that the router uses to route.
     * @var Rule\RuleIface[]
     */
    protected $rules = [];

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Add a rule to the router.
     *
     * @param Rule\RuleIface $rule The rule.
     */
    public function addRule(Rule\RuleIface $rule)
    {
        $this->rules[] = $rule;
    }

    /**
     * Route the URI to the controller and parameters that should respond to it.
     *
     * @param string $uri The URI that we are routing.
     * @return mixed[] The class and parameters that should respond to the URI (generally this should be a Controller).
     */
    public function route($uri)
    {
        // The URI that is routed is continually refined from the initial URI by the rules.
        $refinedURI = $uri;
        $params     = [];

        foreach ($this->rules as $rule) {
            $rule->setURI($refinedURI);

            if ($rule->isMatch()) {
                $refinedURI = $rule->getController();
                $params += $rule->getParams();

                if ($rule->isAuthoritative()) {
                    break;
                }
            }
        }

        return ['controller' => $refinedURI, 'params' => $params];
    }
}
// EOF
