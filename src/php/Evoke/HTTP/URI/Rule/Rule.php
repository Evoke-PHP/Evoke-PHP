<?php
/**
 * HTTP URI Rule Interface
 *
 * @package HTTP\URI\Rule
 */
namespace Evoke\HTTP\URI\Rule;

/**
 * HTTP URI Rule Interface
 *
 * Map the URI to a controller and parameters.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   HTTP\URI\Rule
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