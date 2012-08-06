<?php
namespace Evoke\HTTP\URI;

use \Evoke\HTTP\RequestIface;

/**
 * Router
 *
 * Route the Request to a class and parameters (probably a controller).
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package HTTP
 */
class Router implements RouterIface
{
	/**
	 *  Request Object.
	 *  @var Evoke\HTTP\RequestIface
	 */
	protected $request;

	/**
	 * Rules that the router uses to route.
	 * @var Rule\RuleIface[]
	 */
	protected $rules = array();

	/**
	 * Create a HTTP URI Router that routes the request to a response.
	 *
	 * @param Evoke\HTTP\RequestIface
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
	 * @param Evoke\HTTP\URI\Rule\RuleIface The rule.
	 */
	public function addRule(Rule\RuleIface $rule)
	{
		$this->rules[] = $rule;
	}

	/**
	 * Route the URI to the class and parameters that should respond to it.
	 *
	 * @return mixed[] The class and parameters that should respond to the URI
	 *                 (generally this should be a Controller class).
	 */
	public function route()
	{
		// The classname starts as the request URI and is refined by the mappers
		// until it is able to create the correct classname.
		$classname = $this->request->getURI();
		$params = array();
      
		foreach ($this->rules as $rule)
		{
			if ($rule->isMatch($classname))
			{
				// Set the parameters before updating the classname.
				$params += $rule->getParams($classname);
				$classname = $rule->getClassname($classname);

				if ($rule->isAuthoritative())
				{
					break;
				}
			}
		}

		return array('Class'  => $classname,
		             'Params' => $params);
	}
}
// EOF
