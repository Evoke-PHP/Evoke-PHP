<?php
namespace Evoke\Core\HTTP\URI;

use Evoke\Core\Iface;

/// Receive the request and create the correct response for it.
class Router implements Iface\HTTP\URI\Router
{
	/** @property $factory
	 *  The Factory \object for sending to the response.
	 */
	protected $factory;

	/** @property $request
	 *  Request \object
	 */
	protected $request;

	/** @property $reponse
	 *  Response \object
	 */
	protected $reponse;

	/** @property $rules
	 *  \array of rules that the router uses to route.
	 */
	protected $rules;

	/** Create a HTTP URI Router that routes the request to a response.
	 *  @param Factory  \object Factory for creating the response.
	 *  @param Request  \object Request object.
	 *  @param Response \object Response object.
	 */
	public function __construct(Iface\Factory       $factory,
	                            Iface\HTTP\Request  $request,
	                            Iface\HTTP\Response $response)
	{
		$this->factory       = $factory;
		$this->request       = $request;
		$this->response      = $response;
		$this->rules         = array();
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/** Add a rule to the router.
	 *  @param rule \object HTTP URI Rule object.
	 */
	public function addRule(Iface\HTTP\URI\Rule $rule)
	{
		$this->rules[] = $rule;
	}

	/** Create the object that will respond to the routed URI).
	 *  \return \object The object that will respond (generally a Controller).
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

		/** An exception will be thrown if an unknown class is atttempted to be
		 *  built that should be caught at a higher level.
		 */
		return $this->factory->build($classname,
		                             $params,
		                             $this->factory,
		                             $this->request,
		                             $this->response);
	}
}
// EOF
