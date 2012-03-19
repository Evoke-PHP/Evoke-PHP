<?php
namespace Evoke\Core\HTTP\URI\Rule;

/** A rule to change strings from a request so that a response can be formed.
 *  No parameters are matched by this class.
 */
class Strings extends Base
{
	/** @property $subRules
	 *  \array of string replacements to be performed on the URI to map it to
	 *  a response.
	 */
	protected $subRules;

	/** Construct the URI to Response rule that uses string replacements to map
	 *  the URI to the Response class.
	 *  @param subRules \array The array of string replacements of the form:
	 *  \code
	 *  array('Match'       => 'match_string',
	 *        'Replacement' => 'replacement_string')
	 *  \endcode
	 *  @param authoritative \bool Whether the rule can definitely give the
	 *  final route for all URIs that it matches.
	 */
	public function __construct(Array $subRules, $authoritative=false)
	{
		foreach ($subRules as $subRule)
		{
			if (!isset($subRule['Match'], $subRule['Replacement']))
			{
				throw new \InvalidArgumentException(
					__METHOD__ . ' all rules need a Match and Replacement.');
			}
		}	    
      
		parent::__construct($authoritative);

		$this->subRules = $subRules;
	}
   
	/******************/
	/* Public Methods */
	/******************/

	/** Get the response.
	 *  @param uri \string The URI to get the response from.
	 *  @return \string The uri with the string replacements made.
	 */
	public function getResponse($uri)
	{
		$response = $uri;
		
		foreach ($this->subRules as $subRule)
		{
			$response = str_replace($subRule['Match'],
			                        $subRule['Replacement'],
			                        $response);
		}

		return $response;
	}
	
	/** Check the uri to see if it matches. Only 1 sub-rule needs to match.
	 *  @return \bool Whether the uri is matched.
	 */
	public function isMatch($uri)
	{
		foreach ($this->subRules as $subRule)
		{
			if (strpos($uri, $subRule['Match']) !== false)
			{
				return true;
			}
		}
      
		return false;
	}
}
// EOF