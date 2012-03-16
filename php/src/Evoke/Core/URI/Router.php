<?php
namespace Evoke\Core\URI;

/// Receive the request and create the correct response for it.
class Router implements \Evoke\Core\Iface\URI\Router
{
	/** @property $Factory
	 *  The Factory \object for sending to the response.
	 */
	protected $Factory;

	/** @property $mappings
	 *  The \array of mappings that have been added to the Router for use in
	 *  mapping the URI to the correct response.
	 */
	protected $mappings;

	/** @property $Request
	 *  Request \object
	 */
	protected $Request;

	/** @property $responseBase
	 *  The base \string for the response class.
	 */
	protected $responseBase;
	
	public function __construct(Array $setup)
	{
		$setup +=array('Factory'       => NULL,
		               'Request'       => NULL,
		               'Response_Base' => NULL);

		if (!$setup['Factory'] instanceof \Evoke\Core\Factory)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires Factory');
		}
      
		if (!$setup['Request'] instanceof \Evoke\Core\Iface\URI\Request)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires Request');
		}
		
		if (!is_string($setup['Response_Base']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Response_Base as string');
		}

		$this->mappings = array();

		$this->Factory         = $setup['Factory'];
		$this->Request         = $setup['Request'];
		$this->responseBase    = $setup['Response_Base'];
	}

	/** Append a mapping rule into the Router's mapping list.
	 *  @param map \object Mapper object.
	 */
	public function appendMapper(\Evoke\Core\Iface\URI\Mapper $map)
	{
		$this->mappings[] = $map;
	}

	/** Create the response object (that responds to the routed URI).
	 *  \return \object Response
	 */
	public function createResponse()
	{
		// The response starts as the request URI and is refined by the mappers
		// until it is able to create the correct response object.
		$response = $this->Request->getURI();
		$responseParams = array();
      
		foreach ($this->mappings as $map)
		{
			if ($map->matches($response))
			{
				// Set the parameters for the response before updating the response.
				$responseParams += $map->getParams($response);
				$response = $map->getResponse($response);

				if ($map->isAuthoritative())
				{
					break;
				}
			}
		}

		// Create the response object.
		try
		{
			return $this->Factory->getResponse($this->responseBase . $response,
			                                   $responseParams);
		}
		catch (\Exception $e)
		{
			throw new \Exception(
				__METHOD__ . ' unable to create response: ' . $this->responseBase .
				$response . ' due to: ' . $e->getMessage());
		}
	}

	/** Prepend a mapping rule into the Router's mapping list.
	 *  @param map \object Mapper object.
	 */
	public function prependMapper(\Evoke\Core\Iface\URI\Mapper $map)
	{
		array_unshift($this->mappings, $map);
	}
}
// EOF
