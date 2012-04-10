<?php
namespace Evoke\Core\HTTP;

/// Provide details of the request.
class Request implements \Evoke\Core\Iface\HTTP\Request
{
	/** @property $basicPatterns
	 *  \string Regexp subpatterns to match components of the request header
	 *  values.
	 */
	private $basicPatterns;

	/** @property $params
	 *  \array of parameters for the request ($_GET, $_POST etc.)
	 */
	protected $params;
	
	/** @property $validateHeaders
	 *  \bool Whether to validate the headers before parsing.
	 */
	protected $validateHeaders;
	
	public function __construct(Array $setup=array())
	{
		$setup += array('Validate_Headers' => true);

		$this->validateHeaders = $setup['Validate_Headers'];

		/* Pattern definitions from:
		 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec2.html#sec2.2 and
		 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec3.html#sec3.6 to 3.7.
		 */
		$this->basicPatterns =
			'(?<ATTRIBUTE>     (?&TOKEN))' .
			'(?<CHAR>          [\x00-\x7f])' .
			'(?<CRLF>          \x0d\x0a)' .
			'(?<CTL>           [\x00-\x1f\x7f])' .
			// A shortcut subroutine for the linear whitespace that litters the
			// regex thanks to the following in RFC2616-sec2.1:
			// implied *LWS
			//     The grammar described by this specification is word-based.
			//     Except where noted otherwise, linear white space (LWS) can be
			//     included between any two adjacent words (token or
			//     quoted-string), and between adjacent words and separators,
			//     without changing the interpretation of a field.
			'(?<L>             (?&LWS)*)' .
			'(?<LWS>           (?&CRLF)? (\x09 | \x20)+)' .
			'(?<Q_VALUE>       (0(\.[[:digit:]]{0,3})?) | (1(\.0{0,3})))' .
			'(?<QDTEXT>        [\x09\x0a\x0d\x20\x21\x23-\x7e\x80-\xff])' .
			'(?<QUOTED_PAIR>   \x5c(?&CHAR))' .
			'(?<QUOTED_STRING> "((?&QDTEXT) | (?&QUOTED_PAIR))*")' .
			'(?<SEPARATORS>    [\x09\x20\x22\x28\x29\x2c\x2f\x28\x29\x2c\x2f\x3a-\x40\x5b-\x5d\x7b\x7d])' .
			'(?<TOKEN>         (?&TOKEN_CHAR)+)' .
			'(?<TOKEN_CHAR>    [\x21\x23-\x27\x2a\x2b\x2d\x2e\x30-\x39\x41-\x5a\x5e-\x7a\x7c\x7e])' .
			'(?<VALUE>         (?&TOKEN) | (?&QUOTED_STRING))';
	}

   /******************/
   /* Public Methods */
   /******************/

	/** Get the method.  (One of the HTTP verbs HEAD, GET, OPTIONS, TRACE,
	 *  POST, PUT or DELETE).
	 */
	public function getMethod()
	{
		if (!isset($_SERVER['REQUEST_METHOD']))
		{
			return 'GET';
		}
		
		return $_SERVER['REQUEST_METHOD'];
	}
	
	/** Get the query parameter.
	 *  @param param \string The parameter to get.
	 *  \return \bool The query parameter.
	 */
	public function getQueryParam($param)
	{
		if (!isset($_REQUEST[$param]))
		{
			throw new \LogicException(
				__METHOD__ . ' should only be called if the parameter is set.');
		}

		return $_REQUEST[$param];
	}

	/** Get the query parameters.
	 *  \return The query parameters as an array.
	 */
	public function getQueryParams()
	{
		return $_REQUEST;
	}
	
	/** Get the URI of the request (without the query string).
	 *  \return The URI of the request.
	 */
	public function getURI()
	{
		return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	}

	/** Whether the query parameter is set.
	 *  @param param \string The parameter to check.
	 *  \return \bool Whether the query parameter is set.
	 */
	public function issetQueryParam($param)
	{
		return isset($_REQUEST[$param]);
	}
	
	/** Parse the Accept header field from the request according to:
	 *  http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1
	 *
	 *  This field specifies the preferred media types for responses.
	 *
	 *  \return \array of Accepted media types with their quality factor, ordered
	 *  by preference according to \ref compareAccept.  Each element is of the
	 *  form:
	 *  \verbatim
	 *  array(array('Q_Factor' => 0.5,
	 *              'Subtype'  => 'html',
	 *              'Type'     => 'text'),
	 *        etc.
	 *  \endverbatim
	 */
	public function parseAccept()
	{
		if (!isset($_SERVER['HTTP_ACCEPT']))
		{
			throw new \OutOfBoundsException(
				__METHOD__ . ' HTTP_ACCEPT is not set.');
		}

		$acceptString = $_SERVER['HTTP_ACCEPT'];
		
		$acceptPatterns =
			'(?<ACCEPT_EXTENSION>' .
			'   ;(?&L)(?&TOKEN)(=((?&TOKEN)|(?&QUOTED_STRING)))?)' .
			'(?<SUBTYPE>          (?&TOKEN)|\*)' .
			'(?<TYPE>             (?&TOKEN)|\*)';

		if ($this->validateHeaders)
		{
			$validationPattern =
				'/(?(DEFINE)' . $this->basicPatterns . $acceptPatterns .
				// Accept the ',' separator except  
				'    (?<ACCEPT>' .
				'        (?&ACCEPT_ELEMENT)?((?&L),(?&L)(?&ACCEPT_ELEMENT))*)' .
				'    (?<ACCEPT_ELEMENT> (?&MEDIA_RANGE)(?&L)(?&ACCEPT_PARAMS)?)' .
				'    (?<ACCEPT_PARAMS>' .
				'        (?&L);(?&L)q=(?&Q_VALUE)(?&ACCEPT_EXTENSION)*)' .
				'    (?<MEDIA_RANGE>    (?&L)(?&TYPE)\/(?&SUBTYPE))' .
				')^(?&ACCEPT)$/x';
				
			if (!preg_match($validationPattern, $acceptString))
			{
				throw new \InvalidArgumentException(
					__METHOD__ . ' Accept request header: ' . $acceptString .
					' is invalid.');
			}
		}

		$acceptElementPattern =
			'/(?(DEFINE)' . $this->basicPatterns . $acceptPatterns . ')' .
			// Type/Subtype
			'(?&L)(?<Type>(?&TYPE))\/(?<Subtype>(?&SUBTYPE))' .
			// (;q=Q_Factor)?
			'((?&L);(?&L)q=(?<Q_Factor>(?&Q_VALUE)))?' .
			// Params
			'(?<Params>(?&ACCEPT_EXTENSION)*)' .
			'/x';

		$accepted = array();		
		$numMatches = preg_match_all(
			$acceptElementPattern, $acceptString, $matches);

		if ($numMatches > 0)
		{
			$paramsPattern =
				'/(?(DEFINE)' . $this->basicPatterns . ')' .
				';(?&L)(?<P_KEY>(?&TOKEN))' .
				'(=(?<P_VAL>((?&TOKEN)|(?&QUOTED_STRING))))?' .
				'/x';

			// Loop through each match, storing it in the accepted array.
			for ($i = 0; $i < $numMatches; $i++)
			{
				$qFactor = empty($matches['Q_Factor'][$i]) ? 1.0 :
					$matches['Q_Factor'][$i] + 0.0; // Make it a float.

				// Parse any accept extensions (more extensions makes a difference
				// for the Accept preference ordering).
				$params = array();
				
				if (!empty($matches['Params'][$i]))
				{
					$numParams = preg_match_all($paramsPattern,
					                            $matches['Params'][$i],
					                            $paramsMatches);

					$params = array_combine($paramsMatches['P_KEY'],
					                        $paramsMatches['P_VAL']);
				}
				
				$accepted[] = array(
					'Params'   => $params,
					'Q_Factor' => $qFactor,
					'Subtype'  => $matches['Subtype'][$i],
					'Type'     => $matches['Type'][$i]);
			}
		}
		
		usort($accepted, array($this, 'compareAccept'));
		
		return $accepted;
	}

	/** Parse the Accept-Language header from the request according to:
	 *  http://www.w3.org/Protocols/rfc2616/rfc2616-sec3.html#sec3.10
	 *  http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
	 *
	 *  This field specifies the preferred languages for responses.
	 */
	public function parseAcceptLanguage()
	{
		if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			throw new \OutOfBoundsException(
				__METHOD__ . ' HTTP_ACCEPT_LANGUAGE is not set.');
		}

		$acceptLanguageString = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		$languagePatterns = $this->basicPatterns .
			'(?<ACCEPT_LANGUAGE>' .
			'    (?&ACCEPT_LANGUAGE_ELEMENT)' .
			'    ((?&L),(?&L)(?&ACCEPT_LANGUAGE_ELEMENT))*)' .
			'(?<ACCEPT_LANGUAGE_ELEMENT>' .
			'    (?&L)(?&LANGUAGE_RANGE)((?&L);(?&L)q(?&L)=(?&L)(?&Q_VALUE))?)' .
			'(?<ALPHA_18>                [[:alpha:]]{1,8})' .
			'(?<LANGUAGE_RANGE>          ((?&ALPHA_18)(-(?&ALPHA_18))* | \*))';

		if ($this->validateHeaders)
		{
			$validationPattern =
				'/(?(DEFINE)' . $languagePatterns . ')^(?&ACCEPT_LANGUAGE)$/x';
				
			if (!preg_match($validationPattern, $acceptLanguageString))
			{
				throw new \InvalidArgumentException(
					__METHOD__ . ' Accept request header: ' . $acceptLanguageString .
					' is invalid.');
			}
		}

		// Match the language and its optional Q_Factor.
		$pattern = '/(?(DEFINE)' .	$languagePatterns . ')' .
			'(?<Language>(?&ALPHA_18)(-(?&ALPHA_18))*|\*)' .
			'((?&L);(?&L)q(?&L)=(?&L)(?<Q_Factor>(?&Q_VALUE)))?/x';

		$acceptLanguages = array();
		$numLanguages = preg_match_all($pattern, $acceptLanguageString, $matches);

		for ($i = 0; $i < $numLanguages; $i++)
		{
			// The quality value defaults to 1.
			$qFactor =
				empty($matches['Q_Factor'][$i]) ? 1 : $matches['Q_Factor'][$i];
			
			$acceptLanguages[] = array('Language' => $matches['Language'][$i],
			                             'Q_Factor' => $qFactor);
		}

		usort($acceptLanguages, array($this, 'compareAcceptLanguage'));
		
		return $acceptLanguages;
	}
	
   /*********************/
   /* Protected Methods */
   /*********************/

	/** Compare two accept media types so that they can be sorted via usort.
	 *  @param a \array The first accepted media type.
	 *  @param b \array The second accepted media type.
	 *  \return \int as required by usort.
	 */
	protected function compareAccept(Array $a, Array $b)
	{
		return $this->scoreAccept($b) - $this->scoreAccept($a);
	}

	/** Compare two accept languages so that they can be sorted via usort.
	 *  @param a \array The first accept language.
	 *  @param b \array The second accept language.
	 *  \return \int as required by usort.
	 */
	protected function compareAcceptLanguage(Array $a, Array $b)
	{
		return $this->scoreAcceptLanguage($b) - $this->scoreAcceptLanguage($a);
	}

	
   /*******************/
   /* Private Methods */
   /*******************/
			                    
	/** Score an accept media type so that they can be compared.
	 *  @param accept \array The accept media type array.
	 *  \return \int The score of the accept array for comparison.
	 */
	private function scoreAccept(Array $accept)
	{
		// The Q_Factor dominates, followed by Type, Subtype and then number of
		// parameters. The one unknown is the number of parameters, but we assume
		// that it is less than 10000, so that the score cannot be overriden by
		// a lower level.
		return
			// Normalise to 1                         Multiply by Importance
			(($accept['Q_Factor'] * 1000)             * 1000000) +
			((($accept['Type'] !== '*') ? 1 : 0)      *  900000) +
			((($accept['Subtype'] !== '*') ? 1 : 0)   *   90000) +
			((count($accept['Params']))               *       1);
	}

	/** Score an accept language so that they can be compared.
	 *  @param accept \array The accept language array.
	 *  \return \int The score of the accept language array for comparison.
	 */
	private function scoreAcceptLanguage(Array $acceptLanguage)
	{
		// Make it at least +-1 so that it doesn't evaluate to 0 (i.e equal).
		return $acceptLanguage['Q_Factor'] * 1000;
	}
}
// EOF