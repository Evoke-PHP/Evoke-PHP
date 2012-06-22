<?php
namespace Evoke\Service\Provider\Rule;

/**
 * Exact Rule
 *
 * A Provider interface routing rule that matches the exact interface name and
 * converts it to a specific concrete classname.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Service
 */
class Exact implements RuleIface
{
	/**
	 * The exact classname that this rule converts to.
	 * @var string
	 */
	protected $classname;

	/**
	 * The exact interfaceName that this rule converts from.
	 * @var string
	 */
	protected $interfaceName;

	/**
	 * Construct the Exact rule.
	 *
	 * @param string The interface name to match.
	 * @param string The classname to convert to.
	 */
	public function __construct(/* String */ $interfaceName,
	                            /* String */ $classname)
	{
		$this->classname     = $classname;
		$this->interfaceName = $interfaceName;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Return the exact conversion which is always the constructed classname.
	 *
	 * @param string Interface that we ignore because we know the rule matches
	 *               and what we want to return.
	 *
	 * @return string The classname of the exact match.
	 */
	public function getClassname($interfaceName)
	{
		return $this->classname;
	}
	
	/**
	 * Check to see if the rule matches.
	 *
	 * @param string The interface name to match exactly. (Type is considered
	 *               unimportant == is ok.)
	 *
	 * @return bool Whether the rule matches.
	 */
	public function isMatch($interfaceName)
	{
		return $interfaceName == $this->interfaceName;
	}
}
// EOF