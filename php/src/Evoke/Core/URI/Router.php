<?php
namespace Evoke\Core\URI;

/// Receive the request and create the correct response for it.
class Router implements \Evoke\Core\Iface\URI\Router
{
	/** @property $Factory
	 *  The Factory \object for sending to the response.
	 */
	protected $Factory;

	/** @property $InstanceManager
	 *  The InstanceManager \object for sending to the response.
	 */
	protected $InstanceManager;

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
		$setup +=array('Factory'          => NULL,
		               'Instance_Manager' => NULL,
		               'Request'          => NULL,
		               'Response_Base'    => NULL);

		if (!$setup['Factory'] instanceof \Evoke\Core\Factory)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires Factory');
		}
      
		if (!$setup['Instance_Manager'] instanceof
		    \Evoke\Core\Iface\InstanceManager)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires InstanceManager');
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
		$this->InstanceManager = $setup['Instance_Manager'];
		$this->Request         = $setup['Request'];
		$this->responseBase    = $setup['Response_Base'];
	}

	public function appendMapper(\Evoke\Core\Iface\URI\Mapper $map)
	{
		$this->mappings[] = $map;
	}

	public function createResponse()
	{
		$uri = $this->getURI();
		$params = array();
		$response = '';
      
		foreach ($this->mappings as $map)
		{
			if ($map->matches($uri))
			{
				$params = $map->getParams($uri);
				$response = $map->getResponse($uri);

				if ($map->isAuthoritative())
				{
					break;
				}
				else
				{
					// The response is an enhanced request if we are using chained
					// URI mappers.
					$uri = $response;
				}	    
			}
		}

		$response = $this->responseBase . $response;

		// Create the response object.
		try
		{
			return $this->InstanceManager->create(
				$response,
				array_merge(
					array('Factory'         => $this->Factory,
					      'Instance_Manager' => $this->InstanceManager),
					$params));
		}
		catch (\Exception $e)
		{
			throw new \Exception(
				__METHOD__ . ' unable to create response due to: ' .
				$e->getMessage());
		}
	}
      
	public function prependMapper(\Evoke\Core\Iface\URI\Mapper $map)
	{
		array_unshift($this->mappings, $map);
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	protected function getURI()
	{
		if (!isset($_SERVER['REQUEST_URI']))
		{
			throw new \RuntimeException(__METHOD__ . ' no REQUEST_URI.');
		}
      
		return $_SERVER['REQUEST_URI'];
	}
}
// EOF
