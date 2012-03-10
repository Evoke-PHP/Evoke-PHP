<?php
namespace Evoke\Core\URI;

/// Provide details of the request.
class Request implements \Evoke\Core\Iface\URI\Request
{
	/// Regexp strings to match components of the request header values.
	private $basicPatterns;

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
			'(?<ATTRIBUTE>     (?&TOKEN))' . "\n" .
			'(?<CHAR>          [\x00-\x7f])' . "\n" .
			'(?<CRLF>          \x0d \x0a)' . "\n" .
			'(?<CTL>           [\x00-\x1f\x7f])' . "\n" .
			'(?<LWS>           (?&CRLF)? (\x09 | \x20)+)' . "\n" .
			'(?<NON_CTL>       [^\x00-\x1f\x7f])' . "\n" .
			'(?<Q_VALUE>       (0(\.[[:digit:]]{0,3})?) | (1(\.0{0,3})))' . "\n" .
			'(?<QDTEXT>        [\x09\x0a\x0d\x20\x21\x23-\x7e\x80-\xff])' . "\n" .
			'(?<QUOTED_PAIR>   \x5c(?&CHAR))' . "\n" .
			'(?<QUOTED_STRING> "((?&QDTEXT) | (?&QUOTED_PAIR))*")' . "\n" .
			'(?<SEPARATORS>    [\x09\x20\x22\x28\x29\x2c\x2f\x28\x29\x2c\x2f\x3a-\x40\x5b-\x5d\x7b\x7d])' . "\n" .
			'(?<TOKEN>         (?&TOKEN_CHAR)+)' . "\n" .
			'(?<TOKEN_CHAR>    [\x21\x23-\x27\x2a\x2b\x2d\x2e\x30-\x39\x41-\x5a\x5e-\x7a\x7c\x7e])' . "\n" .
			'(?<VALUE>         (?&TOKEN) | (?&QUOTED_STRING))';
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
			'(?<ACCEPT_EXTENSION>  ;(?&LWS)?(?&TOKEN)' . "\n" .
			'    ((?&LWS)?=(?&LWS)?((?&TOKEN)|(?&QUOTED_STRING)))?)' . "\n" .
			'(?<SUBTYPE>           (?&TOKEN)|\*)' . "\n" .
			'(?<TYPE>              (?&TOKEN)|\*)';

		if ($this->validateHeaders)
		{
			$validationPattern =
				'/(?(DEFINE)' . $this->basicPatterns . $acceptPatterns .
				'   (?<ACCEPT>' .
				'      ((?&ACCEPT_ELEMENT)(?&LWS)?,?)*)' .
				'   (?<ACCEPT_ELEMENT>' .
				'      (?&MEDIA_RANGE)(?&LWS)?(?&ACCEPT_PARAMS)?)' .
				'   (?<ACCEPT_PARAMS>' .
				'      (?&LWS)?;(?&LWS)?q(?&LWS)?=(?&LWS)?(?&Q_VALUE)' .
				'      (?&ACCEPT_EXTENSION)*)' .
				'   (?<MEDIA_RANGE>' .
				'      (?&LWS)?(?&TYPE)(?&LWS)?\/(?&LWS)?(?&SUBTYPE))' .
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
			'(?&LWS)?(?<Type>(?&TYPE))' .              // Type
			'(?&LWS)?\/(?&LWS)?' .                  	 // /
			'(?<Subtype>(?&SUBTYPE))' .             	 // Subtype
			'(' .                                   	 // <Optional>
			'   (?&LWS)?;(?&LWS)?q(?&LWS)?=(?&LWS)?' . //    ;q=
			'   (?<Q_Factor>(?&Q_VALUE))' .        	 //    Q_Factor
			')?' .                                     // </Optional>
			'(?<Params>(?&ACCEPT_EXTENSION)*)' .       // Params
			'/x';

		$accepted = array();		
		$numMatches = preg_match_all(
			$acceptElementPattern, $acceptString, $matches);

		if ($numMatches > 0)
		{
			$paramsPattern =
				'/(?(DEFINE)' . $this->basicPatterns . $acceptPatterns . ')' .
				';(?&LWS)?(?<P_KEY>(?&TOKEN))' . "\n" .
				'((?&LWS)?=(?&LWS)?(?<P_VAL>((?&TOKEN)|(?&QUOTED_STRING))))?' .
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

		$languagePatterns = $this->basicPatterns .
			'(?<ALPHA_18> [[:alpha:]]{1,8})';
		
		$pattern = '/(?(DEFINE)' .	$languagePatterns . ')' .
			'((?&ALPHA_18)(-(?&ALPHA_18))*)|\*(;q=(?&Q_VALUE))?/x';

		preg_match_all($pattern, 
		               $_SERVER['HTTP_ACCEPT_LANGUAGE'],
		               $langParse);

		/** \todo Rewrite the Initial Rubbish from previous version.
		$langs = $langParse[1];
		$quals = $langParse[4];

		$numLanguages = count($langs);
		$langArr = array();
		
		for ($num = 0; $num < $numLanguages; $num++)
		{
			$newLang = strtoupper($langs[$num]);
			$newQual = isset($quals[$num]) ?
				(empty($quals[$num]) ? 1.0 : floatval($quals[$num])) : 0.0;
			
			// Choose whether to upgrade or set the quality factor for the
			// primary language.
			$langArr[$newLang] = (isset($langArr[$newLang])) ?
				max($langArr[$newLang], $newQual) : $newQual;
		}
		
		// sort list based on value
		arsort($langArr, SORT_NUMERIC);
		$acceptedLanguages = array_keys($langArr);
		$preferredLanguage = reset($acceptedLanguages);
		
		$this->SessionManager->set(
			$this->langKey, $preferredLanguage);
		*/
	}
	
   /*********************/
   /* Protected Methods */
   /*********************/

	/** Compare two accept media types so that they can be sorted via usort.
	 *  @param a \array The first accepted media type.
	 *  @param b \array The second accepted media type.
	 *  \return \int -1, 0, 1 (as required by usort).
	 */
	protected function compareAccept(Array $a, Array $b)
	{
		return $this->scoreAccept($b) - $this->scoreAccept($a);
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
}
// EOF