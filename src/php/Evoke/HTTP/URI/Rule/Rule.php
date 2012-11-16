<?php
namespace Evoke\HTTP\URI\Rule;

use InvalidArgumentException;

/**
 * Rule
 *
 * Map the URI to a controller and parameters.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package HTTP
 */
abstract class Rule implements RuleIface
{
	/**
	 * Whether the rule can definitely give the final route for all URIs that
	 * it matches.
	 * @var bool
	 */
	protected $authoritative;

	/**
	 * Construct the URI Rule.
	 *
	 * @param bool Whether the rule can definitely give the final route for all
	 *             URIs that it matches.
	 */
	public function __construct(/* Bool */ $authoritative)
	{
		if (!is_bool($authoritative))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires authoritative as a bool');
		}

		$this->authoritative = $authoritative;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the parameters for the URI.
	 *
	 * @param string The URI.
	 * @return [] Empty Array. (By default no parameters are captured)
	 */	
	public function getParams($uri)
	{
		return array();
	}

	/**
	 * Check whether the rule is authoritative.
	 *
	 * @return bool Whether the rule can definitely give the final route for all
	 *              URIs that it matches.
	 */
	public function isAuthoritative()
	{
		return $this->authoritative;
	}
}
// EOF