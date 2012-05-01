<?php
namespace Evoke\HTTP\URI\Rule;

/** A rule to prepend a string to the classname.
 *  No parameters are matched by this class.
 */
class Prepend extends \Evoke\HTTP\URI\Rule
{
	/** @property $str
	 *  \string String to prepend to the classname.
	 */
	protected $str;

	/** Construct the prepend rule.
	 *  @param str \string The string to prepend.
	 *  @param authoritative \bool Whether the rule can definitely give the
	 *  final route for all URIs that it matches.
	 */
	public function __construct(/* String */ $str,
	                            /* Bool   */ $authoritative = false)
	{
		if (!is_string($str))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires str as string');
		}
      
		parent::__construct($authoritative);

		$this->str = $str;
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/** Get the classname.
	 *  @param uri \string The URI to get the classname from.
	 *  @return \string The uri with the string prepended.
	 */
	public function getClassname($uri)
	{
		return $this->str . $uri;
	}
	
	/** The prepend rule always matches.
	 *  @return \bool TRUE.
	 */
	public function isMatch($uri)
	{
		return true;
	}
}
// EOF