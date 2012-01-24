<?php
namespace Evoke\Core\URI;

/// A URI mapper based on regular expressions.
class MapperRegex extends Mapper
{
   /** Create a MapperRegex.
    *  @param setup \array The setup array is very powerful.  It is used to
    *  perform a two level Regexp on the URI.
    *
    *  The first level is used to:
    *  -# Check that the URI is matched by the mapper.
    *  -# Generate the subject for each named parameter and the response
    *     by replacing the match with the URI_Replacement specified at
    *     the second levels.
    *
    *  At the second level the Params array lists parameters that can be
    *  retrieved from the URI, specifying their Name, Optionality and Value.
    *  The Name and Value are calculated at the second level using:
    *  -# Their specified Pattern.
    *  -# Their specified Replacement.
    *  -# The subject calculated from their URI_Replacement with the first
    *     level Regexp.
    *
    *  Also at the second level is the Response with its second level Regexp.
    *  This should caluclate the response class or it should enhance the URI if
    *  it is used as part of a chain of Mappers.
    *  Again it is calculated by:
    *  -# Its specified Pattern.
    *  -# Its specified Replacement.
    *  -# Its subject calculated from their URI_Replacement with the first level
    *     Regexp.
    *
    *  The first level Regexp is specified by the Match key in the setup array.
    *
    *  The second level Regexps are specified in two places:
    *  - The parameter entries within the Params key (which holds an array of
    *    parameter entries).  In each parameter entry their is a second level
    *    Regexp for the Name and Value keys.
    *  - The Response key.
    *
    *  The whole structure is outlined below ('//' is a Regexp String):
    *     
       \verbatim
       // First Level Regexp.
       'Match'    => '//',
       // The definition of the parameters with their second level Regexps.
       'Params'   => array(
          array('Name'     => array('Pattern'         => '//',
		                    'Replacement'     => '//',
				    'URI_Replacement' => '//')
		'Optional' => true',
		'Value'    => array('Pattern'         => '//',
		                    'Replacement'     => '//',
				    'URI_Replacement' => '//'),
       // The second level Regexp used to calculate the Response class (or the
       // URI for the response class) if this is a chained non-authoritative
       // Mapper.
       'Response' => array('Pattern'         => '//',
                           'Replacement'     => '//',
			   'URI_Replacement' => '//')
      \endverbatim		   
   */ 
   public function __construct(Array $setup)
   {
      $setup += array('Match'        => NULL,
		      'Params'       => NULL,
		      'Response'     => NULL);

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
	    
      if (!is_string($setup['Response']))
      {
	 throw new \InvalidArgumentException(
	    __METHOD__ . ' requires Response as string');
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
	 if (!isset($paramSpec['Name'], $paramSpec['Optional'], $paramSpec['Value']))
	 {
	    throw new \DomainException(
	       __METHOD__ . ' param spec: ' . var_export($paramSpec, true) .
	       ' does not follow Name, Optional, Value format.');
	 }

	 $name = $this->getMappedValue($paramSpec['Name'], $uri);
	 $value = $this->getMappedValue($paramSpec['Value'], $uri);

	 $params[$name] = $value;
      }

      return $params;
   }

   /** Get the response class string for the URI.
    *  \return \string The fully namespaced response class string.
    */
   public function getResponseClass($uri)
   {
      return $this->getMappedValue($this->setup['Response'], $uri);
   }
   
   /*******************/
   /* Private Methods */
   /*******************/

   /** Perform the two level regular expression on the URI.
    *  @param secondLevelRegexp \array The second level Regexp.
    *  @param uri \string The URI.
    *  \return \string The value obtained from the two level Regexp.
    */
   private function getMappedValue($secondLevelRegexp, $uri)
   {
      if (!preg_match($this->setup['Match'], $uri))
      {
	 throw new \RuntimeException(
	    __METHOD__ .  ' Mapper does not match URI.');
      }
      
      if (!isset($secondLevelRegexp['Pattern'],        
		 $secondLevelRegexp['Replacement'],   
		 $secondLevelRegexp['URI_Replacement']))
      {
	 throw new \DomainException(
	    __METHOD__ . ' secondLevelRegexp: ' .
	    var_export($secondLevelRegexp, true) . ' does not follow ' .
	    'Pattern, Replacement, URI_Replacement format.');
      }

      $subject = preg_replace($this->setup['Match'],
			      $secondLevelRegexp['URI_Replacement'],
			      $uri);

      if (!preg_match($secondLevelRegexp['Pattern'], $subject))
      {
	 throw new \RuntimeException(
	    __METHOD__ . ' Second level pattern: ' .
	    var_export($secondLevelRegexp['Pattern'], true) .
	    ' does not match subject: ' . var_export($subject, true));
      }
      
      return preg_replace($secondLevelRegexp['Pattern'],
			  $secondLevelRegexp['Replacement'],
			  $subject);
   }
}
// EOF
