<?php
namespace Evoke\Core\HTTP\URI;

use Evoke\Core\Iface;

/// Receive the request and create the correct response for it.
class Router extends \Evoke\Core\Router
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
	
	public function __construct(\Evoke\Core\Factory $Factory,
	                            Iface\HTTP\Request $Request,
	                            $responseBase)
	{
		parent::__construct();

		$this->Factory         = $Factory;
		$this->Request         = $Request;
		$this->responseBase    = $responseBase;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/** Add a rule to the router.
	 *  @param rule \object HTTP URI Mapper object.
	 */
	public function addRule($rule)
	{
		if (!$rule instanceof Iface\HTTP\URI\Mapper)
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' rule must be a HTTP\URI\Mapper');
		}
		
		$this->rules[] = $rule;
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
			if ($rule->matches($response))
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
			                                   $this->Request,
			                                   $responseParams);
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
