<?php
namespace Evoke\Core\HTTP\MediaType;

use Evoke\Core\Iface;

/** Route the Accepted Media Types from the request to the correct output
 *  format.
 */
class Router extends \Evoke\Core\Router
{
	/******************/
	/* Public Methods */
	/******************/

	/** Append a mapping rule into the Router's mapping list.
	 *  @param map \object Mapper object.
	 */
	public function add($rule)
	{
		if (!$rule instanceof Iface\HTTP\MediaType\Mapper)
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' rule must be HTTP\MediaType\Mapper');
		}
		
		$this->rules[] = $rule;
	}

	/** Select the output format (that responds to the routed MediaType).
	 *  @return \object Response
	 *  @throws OutOfBoundsException When no output format can be chosen that
	 *  matches the Accepted Media Types.
	 */
	public function route(Array $acceptedMediaTypes)
	{
		foreach ($acceptedMediaTypes as $mediaType)
		{
			foreach ($this->rules as $rule)
			{
				if ($rule->isMatch($mediaType))
				{
					return $rule->mapToOutputFormat();
				}
			}
		}

		throw new \OutOfBoundsException(
			__METHOD__ . ' no output formats match the Accepted Media Types.');
	}
}
// EOF
