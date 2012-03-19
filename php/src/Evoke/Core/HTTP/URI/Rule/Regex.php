<?php
namespace Evoke\Core\HTTP\URI\Rule;

/// A powerful rule based on regular expressions.
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

	/** @property $response
	 *  The response to calculate from the URI (see __construct);
	 */
	protected $response;
	
	/** Create a Regex URI Rule.
	 *
	 *  The Regex rule is very powerful.  There are two levels of Regex used
	 *  within each rule.  This allows the URI to be split into the parts which
	 *  represent the response and parameters for the response at the first
	 *  level, and then refined at the second level from those specific parts.
	 *
	 *  The first level of Regex is set with the match parameter and is used to:
	 *  -# Check whether the rule matches the URI.
	 *  -# Capture the subpatterns within the URI that are used at the second
	 *     level.
	 *
	 *  The second level Regex's are defined in the params and response
	 *  parameters and use the subpattern matches from the first level Regex to
	 *  create the subject for the second level regex.
	 *
	 *  @param match \string The first level regex of the form:
	 *  \code
	 *  '/regex_(goes_)?here/'
	 *  \endcode
	 *   used to:
	 *  -# Check that the URI is matched by the rule.
	 *  -# Provide the capture subpatterns used by the second level Regex for
	 *     the params and response parameters.
	 *
	 *  @param response \array The second level regex for calculating the
	 *  response for the URI.  The response array is of the form:
	 *  \code
	 *  array('Pattern'         => '//',
	 *        'Replacement'     => '//',
	 *        'URI_Replacement' => '//')
	 *  \endcode
	 *  It is used to calculate the response string that represents the class
	 *  that responds to the URI.  The calculation is done in two levels:
	 *  -# The first level uses the match property as the pattern, and the
	 *     URI_Replacement as the replacement.
	 *  -# The second level uses the Pattern and Replacement from the response
	 *     array against the subject calculated from the first level
	 *     replacement.
	 *
	 *  @param params \array The second level regex for capturing the parameters
	 *  that should be used to build the Response object.  The parameters are
	 *  are specified in the form:
	 *  \code
	 *  array(array('Name'     => array('Pattern'         => '//',
	 *                                  'Replacement'     => '//',
	 *                                  'URI_Replacement' => '//'),
	 *              'Required' => true,
	 *              'Value'    => array('Pattern'         => '//',
	 *                                  'Replacement'     => '//',
	 *                                  'URI_Replacement' => '//')),
	 *        etc.)
	 *  \endcode
	 *  This builds an array of parameters from the URI.  Each parameter has its
	 *  Name and Value calculated by two levels of Regex (using the Name or
	 *  Value subarray):
	 *  -# The first level uses the match property as the pattern, and the
	 *     URI_Replacement as the replacement.
	 *  -# The second level uses the Pattern and Replacement from the subarray
	 *     against the subject calculated from the first level replacement.
	 *
	 *  @param authoritative \bool Whether the rule can definitely give the
	 *  final route for all URIs that it matches.
	 */
	public function __construct(/*s*/ $match,
	                            Array $response,
	                            Array $params,
	                            /*b*/ $authoritative=false)
	{
		if (!is_string($match))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires match as string');
		}

		try
		{
			$this->ensureSecondLevelRegexp($response);
		}
		catch (Exception $Ex)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' response needs valid second level ' .
				'regexp. Failed with message: ' . $Ex->getMessage());
		}

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
			
			try
			{
				$this->ensureSecondLevelRegexp($paramEntry['Name']);
			}
			catch (Exception $Ex)
			{
				throw new \InvalidArgumentException(
					__METHOD__ . ' param entry for Name at key: ' . $paramKey .
					' needs valid second level regexp. Failed with message: ' .
					$Ex->getMessage());
			}

			try
			{
				$this->ensureSecondLevelRegexp($paramEntry['Value']);
			}
			catch (Exception $Ex)
			{
				throw new \InvalidArgumentException(
					__METHOD__ . ' param entry for Value at key: ' . $paramKey .
					' needs valid second level regexp. Failed with message: ' .
					$Ex->getMessage());
			}			
		}
		
		parent::__construct($authoritative);

		$this->match    = $match;
		$this->params   = $params;
		$this->response = $response;
	}

	/******************/
	/* Public Methods */
	/******************/

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

	/** Get the response.
	 *  @param uri \string The URI to get the response from.
	 *  @return \string The uri with the response regex applied.
	 */
	public function getResponse($uri)
	{
		return $this->getMappedValue($this->response, $uri);
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
		
		if (!isset($secondLevel['URI_Replacement']) ||
		    !is_string($secondLevel['URI_Replacement']))
		{
			throw new \DomainException(
				__METHOD__ . ' URI_Replacement needs to be a string.');
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
		           $secondLevelRegex['URI_Replacement']))
		{
			throw new \DomainException(
				__METHOD__ . ' secondLevelRegex: ' .
				var_export($secondLevelRegex, true) . ' does not follow ' .
				'Pattern, Replacement, URI_Replacement format for URI: ' . $uri);
		}

		$subject = preg_replace($this->match,
		                        $secondLevelRegex['URI_Replacement'],
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