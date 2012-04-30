<?php
namespace Evoke\HTTP\URI;

use Evoke\Iface;

/// Receive the request and create the correct response for it.
class Router implements Iface\HTTP\URI\Router
{
	/** @property $request
	 *  @object Request
	 */
	protected $request;

	/** @property $rules
	 *  @array of rules that the router uses to route.
	 */
	protected $rules = array();

	/** Create a HTTP URI Router that routes the request to a response.
	 *  @param Provider @object Provider for creating the response.
	 *  @param Request  @object Request object.
	 */
	public function __construct(Iface\HTTP\Request  $request)
	{
		$this->request = $request;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/** Add a rule to the router.
	 *  @param rule @object HTTP URI Rule object.
	 */
	public function addRule(Iface\HTTP\URI\Rule $rule)
	{
		$this->rules[] = $rule;
	}

	/** Route the URI to the class and parameters that should respond to it.
	 *  @return @array The class and parameters that should respond to the URI
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
