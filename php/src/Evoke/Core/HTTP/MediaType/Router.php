<?php
namespace Evoke\Core\HTTP\MediaType;

use Evoke\Core\Iface;

/** Route the Accepted Media Types from the request to the correct output
 *  format.
 */
class Router extends \Evoke\Core\Router
{
	/** @property $Request
	 *  Request \object
	 */
	protected $Request;
	
	public function __construct(Iface\HTTP\Request $Request)
	{
		parent::__construct();
		
		$this->Request = $Request;
	}	
	
	/******************/
	/* Public Methods */
	/******************/

	/** Add a rule to the router.
	 *  @param rule \object HTTP MediaType Rule object.
	 */
	public function addRule($rule)
	{
		if (!$rule instanceof Iface\HTTP\MediaType\Rule)
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' rule must be HTTP\MediaType\Rule');
		}
		
		$this->rules[] = $rule;
	}

	/** Select the output format (that responds to the routed MediaType).
	 *  @return \string The output format.
	 *  @throws OutOfBoundsException When no output format can be chosen that
	 *  matches the Accepted Media Types.
	 */
	public function route()
	{
		$acceptedMediaTypes = $this->Request->parseAccept();
		
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
