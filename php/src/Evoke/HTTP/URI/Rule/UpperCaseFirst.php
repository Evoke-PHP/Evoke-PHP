<?php
namespace Evoke\HTTP\URI\Rule;

/**
 * UpperCaseFirst
 *
 * A rule to convert the first letter of each word to upper case.
 * No parameters are matched by this class.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package HTTP
 */
class UpperCaseFirst extends Rule
{
	/**
	 * The delimiters define the boundary of words.
	 * @var string[]
	 */
	protected $delimiters;

	/**
	 * Construct the UpperCaseFirst Rule.
	 *
	 * @param string[] Delimiter strings that show the boundary of words.
	 * @param bool     Whether the rule can definitely give the final route for
	 *                 all URIs that it matches.
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

	/**
	 * Get the classname with each word starting in upper case.
	 *
	 * @param string The URI.
	 * @return string The string representing the Classname.
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

	/**
	 * Check the uri to see if it matches.
	 *
	 * @param string The URI.
	 * @return bool Whether the uri is matched.
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