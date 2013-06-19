<?php
/**
 * HTTP Media Type Router Interface
 *
 * @package HTTP\MediaType
 */
namespace Evoke\HTTP\MediaType;

use Evoke\HTTP\RequestIface,
	OutOfBoundsException;

/**
 * HTTP Media Type Router Interface
 *
 * Route the Accepted Media Types from the request to the correct output format.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   HTTP\MediaType
 */
class Router implements RouterIface
{
	/**
	 * Request Object.
	 * @var Evoke\HTTP\RequestIface
	 */
	protected $request;

	/**
	 * Rules that the router uses to route.
	 * @var Evoke\HTTP\MediaType\Rule\RuleIface[]
	 */
	protected $rules;

	/**
	 * Construct a media type router that determines the output format based on
	 * the acceptable media types.
	 *
	 * @param Evoke\HTTP\RequestIface Request object.
	 */
	public function __construct(RequestIface $request)
	{
		$this->request = $request;
		$this->rules   = array();
	}	
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Add a rule to the router.
	 *
	 * @param Evoke\HTTP\MediaType\Rule\RuleIface HTTP MediaType Rule object.
	 */
	public function addRule(Rule\RuleIface $rule)
	{
		$this->rules[] = $rule;
	}

	/**
	 * Select the output format (that responds to the routed MediaType).
	 *
	 * @return string The output format.
	 * @throws OutOfBoundsException When no output format can be chosen that
	 *                              matches the Accepted Media Types.
	 */
	public function route()
	{
		$acceptedMediaTypes = $this->request->parseAccept();
		
		foreach ($acceptedMediaTypes as $mediaType)
		{
			foreach ($this->rules as $rule)
			{
				if ($rule->isMatch($mediaType))
				{
					return $rule->getOutputFormat($mediaType);
				}
			}
		}

		throw new OutOfBoundsException(
			__METHOD__ . ' no output formats match the Accepted Media Types.');
	}
}
// EOF