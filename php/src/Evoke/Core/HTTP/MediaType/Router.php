<?php
namespace Evoke\Core\HTTP\MediaType;

use Evoke\Core\Iface;

/** Route the Accepted Media Types from the request to the correct output
 *  format.
 */
class Router implements Iface\HTTP\MediaType\Router
{
	/** @property $request
	 *  Request \object
	 */
	protected $request;

	/** @property $rules
	 *  \array of rules that the router uses to route.
	 */
	protected $rules;

	/** Create a media type router that determines the output format based on
	 *  the acceptable media types.
	 *  @param Request \object Request object.
	 */
	public function __construct(Iface\HTTP\Request $request)
	{
		$this->request = $request;
		$this->rules   = array();
	}	
	
	/******************/
	/* Public Methods */
	/******************/

	/** Add a rule to the router.
	 *  @param Rule \object HTTP MediaType Rule object.
	 */
	public function addRule(Iface\HTTP\MediaType\Rule $rule)
	{
		$this->rules[] = $rule;
	}

	/** Select the output format (that responds to the routed MediaType).
	 *  @return \string The output format.
	 *  @throws OutOfBoundsException When no output format can be chosen that
	 *  matches the Accepted Media Types.
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

		throw new \OutOfBoundsException(
			__METHOD__ . ' no output formats match the Accepted Media Types.');
	}
}
// EOF
