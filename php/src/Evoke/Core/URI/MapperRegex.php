<?php
namespace Evoke\Core\URI;

/// A powerful URI mapper based on regular expressions.
class MapperRegex extends Mapper
{
	/** Create a MapperRegex.
	 *  @param setup \array The setup array is very powerful.  It is used to
	 *  perform a two level regex on the URI.
	 *
	 *  The first level is used to:
	 *  -# Check that the URI is matched by the mapper.
	 *  -# Generate the subject for each named parameter and the response
	 *     by replacing the match with the URI_Replacement specified at
	 *     the second levels.
	 *
	 *  At the second level the Params array lists the parameters that are to be
	 *  retrieved from the URI.  Each parameter has a second level regex for its
	 *  Name and Value.  There is also a boolean value showing whether the
	 *  parameter is Required.
	 *  The Name and Value are calculated at the second level using:
	 *  -# Their specified Pattern.
	 *  -# Their specified Replacement.
	 *  -# The subject calculated from their URI_Replacement with the first
	 *     level regex.
	 *
	 *  Also at the second level is the Response with its second level regex.
	 *  This should caluclate the response class or it should enhance the URI if
	 *  it is used as part of a chain of Mappers.
	 *  Again it is calculated by:
	 *  -# Its specified Pattern.
	 *  -# Its specified Replacement.
	 *  -# Its subject calculated from their URI_Replacement with the first level
	 *     regex.
	 *
	 *  The first level regex is specified by the Match key in the setup array.
	 *
	 *  The second level regex are specified in two places:
	 *  - The parameter entries within the Params key (which holds an array of
	 *    parameter entries).  In each parameter entry their is a second level
	 *    regex for the Name and Value keys.
	 *  - The Response key.
	 *
	 *  The whole structure is outlined below ('//' is a regex String):
	 *     
	 \verbatim
	 // First Level Regex.
	 'Match'    => '//',
	 // The definition of the parameters with their second level regex.
	 'Params'   => array(
	 array('Name'     => array('Pattern'         => '//',
	 'Replacement'     => '//',
	 'URI_Replacement' => '//'),
	 'Required' => true,
	 'Value'    => array('Pattern'         => '//',
	 'Replacement'     => '//',
	 'URI_Replacement' => '//'),
	 // The second level Regex used to calculate the Response class (or the
	 // URI for the response class) if this is a chained non-authoritative
	 // Mapper.
	 'Response' => array('Pattern'         => '//',
	 'Replacement'     => '//',
	 'URI_Replacement' => '//')
	 \endverbatim		   
	*/ 
	public function __construct(Array $setup)
	{
		$setup += array('Match'    => NULL,
		                'Params'   => NULL,
		                'Response' => NULL);

		if (!is_string($setup['Match']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Match as string');
		}
		
		if (!is_array($setup['Params']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Params as array');
		}	    
      
		if (!is_array($setup['Response']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Response as array');
		}
      
		parent::__construct($setup);
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Determine whether the mapper matches the given URI.
	 *  @param uri \string The URI to check for a match.
	 *  \return \bool Whether the URI is matched by this mapper.
	 */
	public function matches($uri)
	{
		if (preg_match($this->setup['Match'], $uri))
		{
			return true;
		}
	   
		return false;      
	}

	/** Get the parameters for the URI.  An exception will be thrown for URIs
	 *  that aren't matched.  If you want to avoid this then you should call
	 *  matches first to check that the URI is matched by this Mapper.
	 *  \return \array The parameters from the URI.
	 */
	public function getParams($uri)
	{
		$params = array();

		foreach ($this->setup['Params'] as $paramSpec)
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

	/** Get the response class string for the URI.
	 *  \return \string The fully namespaced response class string.
	 */
	public function getResponse($uri)
	{
		return $this->getMappedValue($this->setup['Response'], $uri);
	}
   
	/*******************/
	/* Private Methods */
	/*******************/

	/** Perform the two level regular expression on the URI.
	 *  @param secondLevelRegex \array The second level regex.
	 *  @param uri \string The URI.
	 *  \return \string The value obtained from the two level regex.
	 */
	private function getMappedValue($secondLevelRegex, $uri)
	{
		if (!preg_match($this->setup['Match'], $uri))
		{
			throw new \RuntimeException(
				__METHOD__ .  ' Mapper does not match URI.');
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

		$subject = preg_replace($this->setup['Match'],
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
