<?php
/**
 * URI Router Interface
 *
 * @package Network\URI
 */
namespace Evoke\Network\URI;

use Evoke\Network\RequestIface;

/**
 * URI Router Interface
 *
 * Route the Request to a controller and parameters.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Network\URI
 */
class Router implements RouterIface
{
	/**
	 *  Request Object.
	 *  @var RequestIface
	 */
	protected $request;

	/**
	 * Rules that the router uses to route.
	 * @var Rule\RuleIface[]
	 */
	protected $rules = array();

	/**
	 * Create a URI Router that routes the request to a response.
	 *
	 * @param RequestIface The request to route.
	 */
	public function __construct(RequestIface $request)
	{
		$this->request = $request;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Add a rule to the router.
	 *
	 * @param Rule\RuleIface The rule.
	 */
	public function addRule(Rule\RuleIface $rule)
	{
		$this->rules[] = $rule;
	}

	/**
	 * Route the URI to the controller and parameters that should respond to it.
	 *
	 * @return mixed[] The class and parameters that should respond to the URI
	 *                 (generally this should be a Controller class).
	 */
	public function route()
	{
		// The controller starts as the request URI and is refined by the
		// mappers until it is the correct controller.
		$controller = $this->request->getURI();
		$params = array();
      
		foreach ($this->rules as $rule)
		{
			if ($rule->isMatch($controller))
			{
				// Set the parameters before updating the controller.
				$params += $rule->getParams($controller);
				$controller = $rule->getController($controller);

				if ($rule->isAuthoritative())
				{
					break;
				}
			}
		}

		return array('Controller' => $controller,
		             'Params'     => $params);
	}
}
// EOF
