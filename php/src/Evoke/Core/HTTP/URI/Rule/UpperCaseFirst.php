<?php
namespace Evoke\Core\HTTP\URI\Rule;

/** A rule to strip unwanted characters from a request so that a classname can
 *  be formed.  No parameters are matched by this class.
 */
class UpperCaseFirst extends Base
{
	/** @property $delimiters
	 *  \array of delimiters that specify where a new word begins that should
	 *  have its first letter made upper case.
	 */
	protected $delimiters;

	/** Construct the UpperCaseFirst Rule.
	 *  @param delimiters \array An array of delimiter characters, which signify
	 *  that the following character is the start of a word that should be
	 *  upper cased.
	 *  @param authoritative \bool Whether the rule can definitely give the
	 *  final route for all URIs that it matches.
	 */
	public function __construct(Array      $delimiters,
	                            /* Bool */ $authoritative=false)
	{
		parent::__construct($authoritative);

		$this->delimiters = $delimiters;
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/** Split the string by the delimiters make the first letter uppercase and
	 *  then rejoin the string with the delimiters.
	 *  @return \string The string representing the Classname.
	 */
	public function getClassname($uri)
	{
		$classname = $uri;
		
		foreach ($this->delimiters as $delimiter)
		{
			$parts = explode($delimiter, $classname);

			foreach ($parts as &$part)
			{
				$part = ucfirst($part);
			}

			$classname = implode($delimiter, $parts);
		}

		return $classname;
	}

	/** Check the uri to see if it matches.
	 *  \return \bool Whether the uri is matched.
	 */
	public function isMatch($uri)
	{
		foreach ($this->delimiters as $delimiter)
		{
			if (strpos($uri, $delimiter) !== false)
			{
				return true;
			}
		}
      
		return false;
	}
}
// EOF