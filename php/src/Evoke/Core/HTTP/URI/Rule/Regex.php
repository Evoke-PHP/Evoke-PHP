<?php
namespace Evoke\Core\HTTP\URI\Rule;

/** A regex rule to map the uri classname and parameters.  There is a single
 *  match for the URI, with all replacements being made from this match.  If
 *  there are more complex requirements such as optional parameters then the
 *  RegexTwoLevel rule should be used.
 */
class Regex extends Base
{
	/** @property $classname
	 *  Regex replacement \string for the classname.
	 */
	protected $classname;

	/** @property $match
	 *  Regex match \string for the URI.
	 */
	protected $match;

	/** @property $params
	 *  \Array of parameter key and value replacement regex for the URI.
	 */
	protected $params;


	/** Construct the SimpleReplace Rule.
	 *  @param match         \string The Regex to match the URI with.
	 *  @param classname     \string The classname regex replacement string.
	 *  @param params        \Array  Regexes replacements for the parameters.
	 *  @param authoritative \bool   Is this always the final route?
	 */
	public function __construct(/*s*/ $match,
	                            /*s*/ $classname,
	                            Array $params        = array(),
	                            /*b*/ $authoritative = false)
	{
		if (!is_string($classname))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires classname as string');
		}
		
		if (!is_string($match))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires match as string');
		}

		foreach ($params as $index => $paramSpec)
		{
			// Set the keys to remove need for isset checks.
			$paramSpec += array('Key' => NULL, 'Value' => NULL);

			if (!is_string($paramSpec['Key']))
			{
				throw new \InvalidArgumentException(
					__METHOD__ . ' param spec at index: ' . $index .
					' requires Key as string.');
			}

			if (!is_string($paramSpec['Value']))
			{
				throw new \InvalidArgumentException(
					__METHOD__ . ' param spec at index: ' . $index .
					' requires Value as string.');
			}
		}
		
		parent::__construct($authoritative);

		$this->classname = $classname;
		$this->match     = $match;
		$this->params    = $params;
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/** Get the classname.
	 *  @param uri \string The URI to get the classname from.
	 *  \return \string The uri with the match replaced.
	 */
	public function getClassname($uri)
	{
		return preg_replace($this->match, $this->classname, $uri);
	}

	/** Get any parameters.
	 *  @param uri \string The URI to get the parameters from.
	 *  @return \array Parameters from the URI.
	 */
	public function getParams($uri)
	{
		$params = array();

		foreach ($this->params as $paramSpec)
		{
			$params[preg_replace($this->match, $paramSpec['Key'], $uri)] =
				preg_replace($this->match, $paramSpec['Value'], $uri);
		}

		return $params;		
	}
	
	/** Check the uri to see if it matches.
	 *  \return \bool Whether the uri is matched.
	 */
	public function isMatch($uri)
	{
		return (preg_match($this->match, $uri) > 0);
	}
}
// EOF