<?php
declare(strict_types = 1);
/**
 * Evoke HTTP Request
 *
 * @package Network\HTTP
 */
namespace Evoke\Network\HTTP;

use LogicException;

/**
 * HTTP Request as per RFC2616-sec5
 *
 * @link      http://www.w3.org/Protocols/rfc2616/rfc2616.html
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Network\HTTP
 *
 * @SuppressWarnings(PHPMD.Superglobals)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 */
class Request implements RequestIface
{
    /**
     * Regexp subpatterns for the HTTP ACCEPT header. This depends on PATTERNS_GENERAL or something equivalent being
     * defined.
     * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1
     * @var string
     */
    const PATTERNS_ACCEPT = <<<'EOP_ACCEPT'
        (?<ACCEPT_EXTENSION> ;(?&L)(?&TOKEN)(=((?&TOKEN)|(?&QUOTED_STRING)))?)
        (?<SUBTYPE>          (?&TOKEN)|\*)
        (?<TYPE>             (?&TOKEN)|\*)
EOP_ACCEPT;

    /**
     * Regexp subpatterns for HTTP ACCEPT LANGUAGE header.
     * This depends on PATTERNS_GENERAL or something equivalent being defined.
     * @var string
     */
    const PATTERNS_ACCEPT_LANGUAGE = <<<'EOP_ACCEPT_LANGUAGE'
        (?<ACCEPT_LANGUAGE>         (?&ACCEPT_LANGUAGE_ELEMENT)((?&L),(?&L)(?&ACCEPT_LANGUAGE_ELEMENT))*)
        (?<ACCEPT_LANGUAGE_ELEMENT> (?&L)(?&LANGUAGE_RANGE)((?&L);(?&L)q(?&L)=(?&L)(?&Q_VALUE))?)
        (?<ALPHA_18>                [a-zA-Z]{1,8})
        (?<LANGUAGE_RANGE>          ((?&ALPHA_18)(-(?&ALPHA_18))* | \*))
EOP_ACCEPT_LANGUAGE;

    /**
     * Regexp subpatterns to match components of the request header values.
     *
     * In addition to the standard symbols defined in the RFC there is a
     * shortcut subroutine <L>. This is a crazily frequent symbol due to the
     * linear whitespace that litters the regex thanks to the implied *LWS in
     * RFC2616-sec2.1:
     *     The grammar described by this specification is word-based. Except
     *     where noted otherwise, linear white space (LWS) can be included
     *     between any two adjacent words (token or quoted-string), and
     *     between adjacent words and separators, without changing the
     *     interpretation of a field.
     *
     * It was decided that defining this would create less clutter in the
     * regexp.
     *
     * @var string
     */
    const PATTERNS_GENERAL = <<<'EOP'
        (?<ATTRIBUTE>     (?&TOKEN))
        (?<CHAR>          [\x00-\x7f])
        (?<CRLF>          \x0d\x0a)
        (?<CTL>           [\x00-\x1f\x7f])
        (?<L>             (?&LWS)*)
        (?<LWS>           (?&CRLF)? (\x09 | \x20)+)
        (?<Q_VALUE>       (0(\.[[:digit:]]{0,3})?) | (1(\.0{0,3})))
        (?<QDTEXT>        [\x09\x0a\x0d\x20\x21\x23-\x7e\x80-\xff])
        (?<QUOTED_PAIR>   \x5c(?&CHAR))
        (?<QUOTED_STRING> "((?&QDTEXT) | (?&QUOTED_PAIR))*")
        (?<SEPARATORS>    [\x09\x20\x22\x28\x29\x2c\x2f\x28\x29\x2c\x2f\x3a-\x40\x5b-\x5d\x7b\x7d])
        (?<TOKEN>         (?&TOKEN_CHAR)+)
        (?<TOKEN_CHAR>    [\x21\x23-\x27\x2a\x2b\x2d\x2e\x30-\x39\x41-\x5a\x5e-\x7a\x7c\x7e])
        (?<VALUE>         (?&TOKEN) | (?&QUOTED_STRING))
EOP;

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the method.  (One of the HTTP verbs HEAD, GET, OPTIONS, TRACE, POST, PUT or DELETE).
     */
    public function getMethod()
    {
        if (!isset($_SERVER['REQUEST_METHOD'])) {
            trigger_error('Request method is not set, defaulting to GET.', E_USER_WARNING);

            return 'GET';
        }

        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Get the query parameter.
     *
     * @param string $param The parameter to get.
     * @return mixed The query parameter.
     */
    public function getParam($param)
    {
        if (!isset($_REQUEST[$param])) {
            throw new LogicException(__METHOD__ . ' should only be called if the parameter is set.');
        }

        return $_REQUEST[$param];
    }

    /**
     * Get the query parameters.
     *
     * @return array|mixed[][] The query parameters.
     */
    public function getParams()
    {
        return isset($_REQUEST) ? $_REQUEST : [];
    }

    /**
     * Get the URI for the request.
     *
     * @return string The URI of the request.
     */
    public function getURI()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Whether the query parameter is set.
     *
     * @param string $param The parameter to check.
     * @return bool Whether the query parameter is set.
     */
    public function issetParam($param)
    {
        return isset($_REQUEST[$param]);
    }

    /**
     * Whether the HTTP_ACCEPT header field is valid. This field specifies the preferred media types for responses.
     *
     * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1
     * @return bool
     */
    public function isValidAccept()
    {
        // The Accept header does not appear to be mandatory.
        if (empty($_SERVER['HTTP_ACCEPT'])) {
            return true;
        }

        $validationPattern =
            '/(?(DEFINE)' . self::PATTERNS_GENERAL . self::PATTERNS_ACCEPT .
            '    (?<ACCEPT>' .
            '        (?&ACCEPT_ELEMENT)?((?&L),(?&L)(?&ACCEPT_ELEMENT))*)' .
            '    (?<ACCEPT_ELEMENT> (?&MEDIA_RANGE)(?&L)(?&ACCEPT_PARAMS)?)' .
            '    (?<ACCEPT_PARAMS>' .
            '        (?&L);(?&L)q=(?&Q_VALUE)(?&ACCEPT_EXTENSION)*)' .
            '    (?<MEDIA_RANGE>    (?&L)(?&TYPE)\/(?&SUBTYPE))' .
            ')^(?&ACCEPT)$/x';

        return preg_match($validationPattern, $_SERVER['HTTP_ACCEPT']) === 1;
    }

    /**
     * Whether the HTTP ACCEPT LANGUAGE header is of the correct format.
     *
     */
    public function isValidAcceptLanguage()
    {
        // The Accept-Language header does not appear to be mandatory.
        if (empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            return true;
        }

        $validationPattern =
            '/(?(DEFINE)' . self::PATTERNS_GENERAL .
            self::PATTERNS_ACCEPT_LANGUAGE . ')^(?&ACCEPT_LANGUAGE)$/x';

        return preg_match($validationPattern, $_SERVER['HTTP_ACCEPT_LANGUAGE']) === 1;
    }

    /**
     * Parse the Accept header field from the request according to RFC2616.
     *
     * This field specifies the preferred media types for responses.
     *
     * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1
     * @return array[] Accepted media types with their quality factor, ordered
     *                 by preference according to compareAccept.  Each element
     *                 of the array has keys defining the Params, Q_Factor,
     *                 Subtype and Type.
     */
    public function parseAccept()
    {
        if (empty($_SERVER['HTTP_ACCEPT'])) {
            // The Accept header does not appear to be mandatory.
            return [];
        }

        // Match a "type/subtype (;q=q_factor)? params" list.
        $acceptElementPattern =
            '/(?(DEFINE)' . self::PATTERNS_GENERAL . self::PATTERNS_ACCEPT .
            ')' .
            '(?&L)(?<type>(?&TYPE))\/(?<subtype>(?&SUBTYPE))' .
            '((?&L);(?&L)q=(?<q_factor>(?&Q_VALUE)))?' .
            '(?<params>(?&ACCEPT_EXTENSION)*)' .
            '/x';

        $accepted   = [];
        $numMatches = preg_match_all($acceptElementPattern, $_SERVER['HTTP_ACCEPT'], $matches);

        if ($numMatches > 0) {
            $paramsPattern =
                '/(?(DEFINE)' . self::PATTERNS_GENERAL . ')' .
                ';(?&L)(?<P_KEY>(?&TOKEN))' .
                '(=(?<P_VAL>((?&TOKEN)|(?&QUOTED_STRING))))?' .
                '/x';

            // Loop through each match, storing it in the accepted array.
            for ($match = 0; $match < $numMatches; $match++) {
                $qFactor = empty($matches['q_factor'][$match]) ? 1.0 :
                    $matches['q_factor'][$match] + 0.0; // Make it a float.

                // Parse any accept extensions (more extensions makes a difference for the Accept preference ordering).
                $params = [];

                if (!empty($matches['params'][$match])) {
                    preg_match_all($paramsPattern, $matches['params'][$match], $paramsMatches);

                    $params = array_combine($paramsMatches['P_KEY'], $paramsMatches['P_VAL']);
                }

                $accepted[] = [
                    'params'   => $params,
                    'q_factor' => $qFactor,
                    'subtype'  => $matches['subtype'][$match],
                    'type'     => $matches['type'][$match]
                ];
            }
        }

        usort($accepted, [$this, 'compareAccept']);

        return $accepted;
    }

    /**
     * Parse the Accept-Language header from the request according to:
     * - http://www.w3.org/Protocols/rfc2616/rfc2616-sec3.html#sec3.10
     * - http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
     *
     * This header field specifies the preferred languages for responses.
     *
     * @return array[]
     * The accepted languages from the request in order of quality from highest to lowest.  Each element of the array
     * has keys defining the Language and Q_Factor.
     */
    public function parseAcceptLanguage()
    {
        if (empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            // Accept-Language header is not mandatory.
            return [];
        }

        // Match the language and its optional Q_Factor.
        $pattern = '/(?(DEFINE)' . self::PATTERNS_GENERAL .
            self::PATTERNS_ACCEPT_LANGUAGE . ')' .
            '(?<language>(?&ALPHA_18)(-(?&ALPHA_18))*|\*)' .
            '((?&L);(?&L)q(?&L)=(?&L)(?<q_factor>(?&Q_VALUE)))?/x';

        $acceptLanguages = [];
        $numLanguages    = preg_match_all($pattern, $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);

        for ($lang = 0; $lang < $numLanguages; $lang++) {
            // The quality value defaults to 1.
            $qFactor = empty($matches['q_factor'][$lang]) ? 1.0 : $matches['q_factor'][$lang] + 0.0; // Make it float.

            $acceptLanguages[] = [
                'language' => $matches['language'][$lang],
                'q_factor' => $qFactor
            ];
        }

        usort($acceptLanguages, [$this, 'compareAcceptLanguage']);

        return $acceptLanguages;
    }

    /*********************/
    /* Protected Methods */
    /*********************/

    /**
     * Compare two accept media types so that they can be sorted via usort.
     *
     * @param mixed[] $first  The first accepted media type.
     * @param mixed[] $second The second accepted media type.
     * @return int As required by usort.
     */
    protected function compareAccept(Array $first, Array $second)
    {
        return $this->scoreAccept($second) - $this->scoreAccept($first);
    }

    /**
     * Compare two accept languages so that they can be sorted via usort.
     *
     * @param mixed[] $first  The first accept language.
     * @param mixed[] $second The second accept language.
     * @return int as required by usort.
     */
    protected function compareAcceptLanguage(Array $first, Array $second)
    {
        return $this->scoreAcceptLanguage($second) -
        $this->scoreAcceptLanguage($first);
    }

    /*******************/
    /* Private Methods */
    /*******************/

    /**
     * Score an accept media type so that they can be compared.
     *
     * @param mixed[] $accept The accept media type array.
     * @return int The score of the accept array for comparison.
     */
    private function scoreAccept(Array $accept)
    {
        // The Q_Factor dominates, followed by Type, Subtype and then number of parameters. The one unknown is the
        // number of parameters, but we assume that it is less than 10000, so that the score cannot be overridden by a
        // lower level.
        return
            // Normalise to 1               Multiply by Importance
            (($accept['q_factor'] * 1000) * 1000000) +
            ((($accept['type'] !== '*') ? 1 : 0) * 900000) +
            ((($accept['subtype'] !== '*') ? 1 : 0) * 90000) +
            ((count($accept['params'])) * 1);
    }

    /**
     * Score an accept language so that they can be compared.
     *
     * @param mixed[] $acceptLanguage The accept language array.
     * @return int The score of the accept language array for comparison.
     */
    private function scoreAcceptLanguage(Array $acceptLanguage)
    {
        // Make it at least +-1 so that it doesn't evaluate to 0 (i.e equal).
        return $acceptLanguage['q_factor'] * 1000;
    }
}
// EOF
