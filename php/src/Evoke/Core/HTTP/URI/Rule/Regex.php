<?php
namespace Evoke\Core\HTTP\URI\Rule;

/** A powerful rule based on regular expressions for refining the URI to a
 *  classname and parameters that will respond (generally a Controller).
 */
class Regex extends Base
{
	/** @property $match
	 *  The regex match to determine whether this object matches the URI. 
	 */
	protected $match;

	/** @property $params
	 *  \array The parameters to calculate from the URI (see __construct).
	 */
	protected $params;

	/** @property $classname
	 *  The classname to calculate from the URI (see __construct);
	 */
	protected $classname;
	
	/** Create a Regex URI Rule.
	 *
	 *  The Regex rule is very powerful.  There are two levels of Regex used
	 *  within each rule.
	 *
	 *  The first level of Regex is set with the match argument and is used to:
	 *  -# Check whether the rule matches the URI.
	 *  -# Capture the subpatterns within the URI that are used at the second
	 *     level.
	 *
	 *  Before the second level Regex's are applied its subject is calculated
	 *  using the matched subpatterns from the first level.  This is done using
	 *  the Match_Part in the classname and params arguments.
	 *
	 *  The second level Regex's are defined in the classname and params
	 *  arguments with their Pattern and Replacement.
	 *
	 *  @param match \string The first level regex of the form:
	 *  \code
	 *  '/regex_(goes_)?here/'
	 *  \endcode
	 *   used to:
	 *  -# Check that the URI is matched by the rule.
	 *  -# Provide the capture subpatterns used by the second level Regex for
	 *     the classname and params.
	 *
	 *  @param classname \array The second level regex for calculating the
	 *  classname for the URI.  The classname array is of the form:
	 *  \code
	 *  array('Pattern'     => '//',
	 *        'Replacement' => '//',
	 *        'Match_Part'  => '//')
	 *  \endcode
	 *  It is used to calculate the name of the class that will be instantiated
	 *  to respond to the URI.  The calculation is done in two levels:
	 *  -# The first level uses the match property as the pattern, and the
	 *     Match_Part as the replacement.
	 *  -# The second level uses the Pattern and Replacement from the classname
	 *     array against the subject calculated from the first level
	 *     replacement.
	 *
	 *  @param params \array The second level regex for capturing the parameters
	 *  for the class.  The parameters are are specified in the form:
	 *  \code
	 *  array(array('Name'     => array('Match_Part'  => '//',
	 *                                  'Pattern'     => '//',
	 *                                  'Replacement' => '//'),
	 *              'Required' => true,
	 *              'Value'    => array('Match_Part'  => '//',
	 *                                  'Pattern'     => '//',
	 *                                  'Replacement' => '//')),
	 *        etc.)
	 *  \endcode
	 *  This builds an array of parameters from the URI.  Each parameter has its
	 *  Name and Value calculated by two levels of Regex (using the Name or
	 *  Value subarray):
	 *  -# The first level uses the match property as the pattern, and the
	 *     Match_Part as the replacement.
	 *  -# The second level uses the Pattern and Replacement from the subarray
	 *     against the subject calculated from the first level replacement.
	 *
	 *  @param authoritative \bool Whether the rule can definitely give the
	 *  final route for all URIs that it matches.
	 */
	public function __construct(/*s*/ $match,
	                            Array $classname,
	                            Array $params,
	                            /*b*/ $authoritative=false)
	{
		if (!is_string($match))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires match as string');
		}

		$this->ensureSecondLevelRegexp($classname);

		foreach ($params as $paramKey => $paramEntry)
		{
			if (!isset($paramEntry['Name'],
			           $paramEntry['Required'],
			           $paramEntry['Value']) ||
			    !is_array($paramEntry['Name']) ||
			    !is_bool($paramEntry['Required']) ||
			    !is_array($paramEntry['Value']))
			{
				throw new \InvalidArgumentException(
					__METHOD__ . ' param entry must contain Name as array, ' .
					'Required as bool and Value as array.');
			}
			
			$this->ensureSecondLevelRegexp($paramEntry['Name']);
			$this->ensureSecondLevelRegexp($paramEntry['Value']);
		}
		
		parent::__construct($authoritative);

		$this->match    = $match;
		$this->params   = $params;
		$this->classname = $classname;
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Get the classname.
	 *  @param uri \string The URI to get the classname from.
	 *  @return \string The uri with the classname regex applied.
	 */
	public function getClassname($uri)
	{
		return $this->getMappedValue($this->classname, $uri);
	}

	/** Get the parameters for the URI.  An exception will be thrown for URIs
	 *  that aren't matched.  If you want to avoid this then you should call
	 *  matches first to check that the URI is matched by this Rule.
	 *  \return \array The parameters from the URI.
	 */
	public function getParams($uri)
	{
		$params = array();

		foreach ($this->params as $paramSpec)
		{
			if (!isset($paramSpec['Name'], $paramSpec['Required'], $paramSpec['Value']))
			{
				throw new \DomainException(
					__METHOD__ . ' param spec: ' . var_export($paramSpec, true) .
					' does not follow Name, Required, Value format.');
			}
	      
			try
			{
				// getMappedValue will throw an UnexpectedValueException if the
				// second level regex does not match.  This is fine if the parameter
				// is not required.
				$params[$this->getMappedValue($paramSpec['Name'], $uri)] =
					$this->getMappedValue($paramSpec['Value'], $uri);
			}
			catch (\UnexpectedValueException $e)
			{
				// If it is required though we are in trouble, rethrow it.
				if ($paramSpec['Required'])
				{
					throw $e;
				}
			}
		}

		return $params;
	}

	/** Determine whether the rule matches the given URI.
	 *  @param uri \string The URI to check for a match.
	 *  @return \bool Whether the URI is matched by this rule.
	 */
	public function isMatch($uri)
	{
		if (preg_match($this->match, $uri))
		{
			return true;
		}
	   
		return false;      
	}

	/*******************/
	/* Private Methods */
	/*******************/

	/** Ensure the second level regexp is defined correctly.
	 *  @throws DomainException if it is not defined correctly.
	 */
	private function ensureSecondLevelRegexp($secondLevel)
	{
		if (!isset($secondLevel['Match_Part']) ||
		    !is_string($secondLevel['Match_Part']))
		{
			throw new \DomainException(
				__METHOD__ . ' Match_Part needs to be a string.');
		}

		if (!isset($secondLevel['Pattern']) ||
		    !is_string($secondLevel['Pattern']))
		{
			throw new \DomainException(
				__METHOD__ . ' Pattern needs to be a string.');
		}

		if (!isset($secondLevel['Replacement']) ||
		    !is_string($secondLevel['Replacement']))
		{
			throw new \DomainException(
				__METHOD__ . ' Replacement needs to be a string.');
		}		
	}
	
	/** Perform the two level regular expression on the URI.
	 *  @param secondLevelRegex \array The second level regex.
	 *  @param uri \string The URI.
	 *  @return \string The value obtained from the two level regex.
	 */
	private function getMappedValue($secondLevelRegex, $uri)
	{
		if (!preg_match($this->match, $uri))
		{
			throw new \RuntimeException(
				__METHOD__ .  ' Rule does not match URI: ' . $uri);
		}
      
		if (!isset($secondLevelRegex['Pattern'],        
		           $secondLevelRegex['Replacement'],   
		           $secondLevelRegex['Match_Part']))
		{
			throw new \DomainException(
				__METHOD__ . ' secondLevelRegex: ' .
				var_export($secondLevelRegex, true) . ' does not follow ' .
				'Pattern, Replacement, Match_Part format for URI: ' . $uri);
		}

		$subject = preg_replace($this->match,
		                        $secondLevelRegex['Match_Part'],
		                        $uri);

		if (!preg_match($secondLevelRegex['Pattern'], $subject))
		{
			throw new \UnexpectedValueException(
				__METHOD__ . ' Second level pattern: ' .
				var_export($secondLevelRegex['Pattern'], true) .
				' does not match subject: ' . var_export($subject, true));
		}

		return preg_replace($secondLevelRegex['Pattern'],
		                    $secondLevelRegex['Replacement'],
		                    $subject);
	}
}
// EOF