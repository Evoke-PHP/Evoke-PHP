<?php
namespace Evoke\Core\HTTP\URI;

use Evoke\Core\Iface;

/// Receive the request and create the correct response for it.
class Router implements Iface\HTTP\URI\Router
{
	/** @property $Factory
	 *  The Factory \object for sending to the response.
	 */
	protected $Factory;

	/** @property $Request
	 *  Request \object
	 */
	protected $Request;

	/** @property $responseBase
	 *  The base \string for the response class.
	 */
	protected $responseBase;

	/** @property $rules
	 *  \array of rules that the router uses to route.
	 */
	protected $rules;

	/** Create a HTTP URI Router that routes the request to a response.
	 *  @param Factory \object Factory for creating the response.
	 *  @param Request \object Request object.
	 *  @param responseBase \string Base class string for the response.
	 */
	public function __construct(Iface\Factory $Factory,
	                            Iface\HTTP\Request $Request,
	                            $responseBase)
	{
		if (!is_string($responseBase))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' responseBase must be a string.');
		}
		
		$this->Factory      = $Factory;
		$this->Request      = $Request;
		$this->responseBase = $responseBase;
		$this->rules        = array();
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/** Add a rule to the router.
	 *  @param rule \object HTTP URI Rule object.
	 */
	public function addRule(Iface\HTTP\URI\Rule $Rule)
	{
		$this->rules[] = $Rule;
	}

	/** Create the response object (that responds to the routed URI).
	 *  \return \object Response
	 */
	public function route()
	{
		// The response starts as the request URI and is refined by the mappers
		// until it is able to create the correct response object.
		$response = $this->Request->getURI();
		$responseParams = array();
      
		foreach ($this->rules as $rule)
		{
			if ($rule->isMatch($response))
			{
				// Set the parameters for the response before updating the response.
				$responseParams += $rule->getParams($response);
				$response = $rule->getResponse($response);

				if ($rule->isAuthoritative())
				{
					break;
				}
			}
		}

		// Create the response object.
		try
		{
			return $this->Factory->getResponse($this->responseBase . $response,
			                                   $responseParams,
			                                   $this->Factory,
			                                   $this->Request);
		}
		catch (\Exception $e)
		{
			throw new \Exception(
				__METHOD__ . ' unable to create response: ' . $this->responseBase .
				$response . ' due to: ' . $e->getMessage());
		}
	}
}
// EOF
