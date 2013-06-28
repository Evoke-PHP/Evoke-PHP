<?php
/**
 * HTTP Media Type Router Interface
 *
 * @package Network\HTTP\MediaType
 */
namespace Evoke\Network\HTTP\MediaType;

use Evoke\Network\HTTP\RequestIface,
	OutOfBoundsException,
	Rule\RuleIface;

/**
 * HTTP Media Type Router Interface
 *
 * Route the Accepted Media Types from the request to the correct output format.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Network\HTTP\MediaType
 */
class Router implements RouterIface
{
	/**
	 * Request Object.
	 * @var RequestIface
	 */
	protected $request;

	/**
	 * Rules that the router uses to route.
	 * @var RuleIface[]
	 */
	protected $rules;

	/**
	 * Construct a media type router that determines the output format based on
	 * the acceptable media types.
	 *
	 * @param RequestIface Request object.
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
	 * @param RuleIface HTTP MediaType Rule object.
	 */
	public function addRule(RuleIface $rule)
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