<?php
namespace Evoke\Service\Provider\Rule;

/** A Provider interface routing rule that matches the exact interface name and
 *  converts it to a specific concrete classname.
 */
class Exact implements RuleIface
{
	/** @property $classname
	 *  @string The exact classname that this rule converts to.
	 */
	protected $classname;

	/** @property $interfaceName
	 *  @string The exact interfaceName that this rule converts from.
	 */
	protected $interfaceName;

	/** Construct the Exact rule.
	 *  @param interfaceName @string The interface name to match.
	 *  @param classname     @string The classname to convert to.
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

	/** Return the exact conversion which is always the constructed classname.
	 *  @param interfaceName @string Interface that we ignore because we know
	 *                               the rule matches and what we want to
	 *                               return.
	 *  @return @string The classname of the exact match.
	 */
	public function getClassname($interfaceName)
	{
		return $this->classname;
	}
	
	/** Check to see if the rule matches.
	 *  @param interfaceName @string The interface name to match exactly. (Type
	 *                               is considered unimportant == is ok.)
	 *  @return @bool Whether the rule matches.
	 */
	public function isMatch($interfaceName)
	{
		return $interfaceName == $this->interfaceName;
	}
}
// EOF