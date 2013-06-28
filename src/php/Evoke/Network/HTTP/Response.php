<?php
/**
 * Evoke HTTP Response
 *
 * @package Network\HTTP
 */
namespace Evoke\Network\HTTP;

use InvalidArgumentException,
	LogicException;

/**
 * HTTP Response 
 *
 * The HTTP Response as per RFC2616 and to a lesser extent RFC1945.  Links in
 * this documentation will generally refer to RFC2616.
 *
 * It is important to understand that PHP is generally run as mod_php or under
 * a similar environment where the HTTP response headers are calculated
 * automatically.
 *
 * @link      http://tools.ietf.org/html/rfc1945
 * @link      http://www.w3.org/Protocols/rfc2616/rfc2616.html
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Network\HTTP
 */
class Response implements ResponseIface
{
	/**
	 * The body of the response.
	 *
	 * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec7.html#sec7.2
	 *
	 * @var string
	 */
	protected $body = '';

	/**
	 * The headers for the response (Field_Name => Field_Value).
	 *
	 * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec4.html#sec4.5
	 * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html#sec6.2
	 * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec7.html#sec7.1
	 *
	 * @var string[]
	 */
	protected $headers = array();

	/**
	 * The HTTP status code for automata (and intelligent humans).
	 *
	 * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html#sec6.1.1
	 *
	 * @var int
	 */
	protected $statusCode;

	/**
	 * The HTTP status reason for humans.
	 *
	 * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html#sec6.1.1
	 *
	 * @var string
	 */
	protected $statusReason;

	/**
	 * HTTP protocol version (1.0, 1.1, etc.).
	 *
	 * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec3.html#sec3.1
	 *
	 * @var string
	 */
	protected $httpVersion;

	/**
	 * Construct a Response object.
	 *
	 * @param string HTTP Version (1.0, 1.1, etc.)
	 */
	public function __construct($httpVersion = '1.1')
	{
		// Ensure httpVersion matches the spec. 
		if (!preg_match('(\d+\.\d+)', $httpVersion))
		{
			throw new InvalidArgumentException(
				'HTTP Version must match Augmented BNF: 1*DIGIT "." 1*DIGIT');
		}
		
		$this->httpVersion = $httpVersion;
	}

	/******************/
	/* Public Methods */
	/******************/
	
	/**
	 * Send the Response as per RFC2616-sec6.
	 */
	public function send()
	{
		if (headers_sent())
		{
			throw new LogicException('Headers have already been sent');
		}
        
		if (!isset($this->statusCode))
		{
			throw new LogicException('HTTP Response code must be set.');
		}
		
		$statusLine = 'HTTP/' . $this->httpVersion . ' ' . $this->statusCode;
		$statusReason = $this->getStatusReason();

		if ($statusReason)
		{
			$statusLine .= ' ' . $statusReason;
		}
		
		header($statusLine);
        
		foreach ($this->headers as $field => $value)
		{
			header($field . ': ' . $value);
		}
            
		echo $this->body;
	}
    
	/**
	 * Set the body of the response.
	 *
	 * @param string The text to set the response body to.
	 */
	public function setBody($text)
	{
		$this->body = $text;
	}
    
	/**
	 * Set the headers to show that the document should be cached.

	 * This must be called before any output is sent (otherwise the headers will
	 * have already been sent).
	 *
	 * @param int The number of days to cache the document for.
	 * @param int The number of hours to cache the document for.
	 * @param int The number of minutes to cache the document for.
	 * @param int The number of seconds to cache the document for.
	 */
	public function setCache($days=0, $hours=0, $minutes=0, $seconds=0)
	{
		// Calculate the offset in seconds.
		$offset = ((((($days * 24) + $hours) * 60) + $minutes) * 60) + $seconds;

		$this->setHeader('Pragma', 'public');
		$this->setHeader('Cache-Control', 'must-revalidate maxage=' . $offset);
		$this->setHeader('Expires',
		                 gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT');
	}

	/**
	 * Set the header field in the response.
	 *
	 * The valid header fields are defined in RFC2616 sections 4.5, 6.2, 7.1.
	 * (Note: 7.1 extension-header allows for the wide range of headers that we
	 * match here, even though they may be ignored by clients.)
	 *
	 * @param string The header to set.
	 * @param string The value to set it to.
	 */
	public function setHeader($field, $value)
	{
		// RFC2616-sec4.2 Field names are case-insensitive.
		$this->headers[strtoupper($field)] = $value;
	}
        
	/**
	 * Set the HTTP status code and reason (200 OK, 404 Not Found, etc.)
	 *
	 * @param int The HTTP status code.
	 * @param string The HTTP status reason.
	 */
	public function setStatus($code, $reason = NULL)
	{
		$this->statusCode = $code;
		$this->statusReason = $reason;
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	/**
	 * Get the status reason, defaulting to HTTP 1.1 recommendations.
	 *
	 * @return string The status reason.
	 */
	protected function getStatusReason()
	{
		if (isset($this->statusReason))
		{
			return $this->statusReason;
		}

		$defaultReasons = array(
			"100"  => "Continue",
			"101"  => "Switching Protocols",
			"200"  => "OK",
			"201"  => "Created",
			"202"  => "Accepted",
			"203"  => "Non-Authoritative Information",
			"204"  => "No Content",
			"205"  => "Reset Content",
			"206"  => "Partial Content",
			"300"  => "Multiple Choices",
			"301"  => "Moved Permanently",
			"302"  => "Found",
			"303"  => "See Other",
			"304"  => "Not Modified",
			"305"  => "Use Proxy",
			"307"  => "Temporary Redirect",
			"400"  => "Bad Request",
			"401"  => "Unauthorized",
			"402"  => "Payment Required",
			"403"  => "Forbidden",
			"404"  => "Not Found",
			"405"  => "Method Not Allowed",
			"406"  => "Not Acceptable",
			"407"  => "Proxy Authentication Required",
			"408"  => "Request Time-out",
			"409"  => "Conflict",
			"410"  => "Gone",
			"411"  => "Length Required",
			"412"  => "Precondition Failed",
			"413"  => "Request Entity Too Large",
			"414"  => "Request-URI Too Large",
			"415"  => "Unsupported Media Type",
			"416"  => "Requested range not satisfiable",
			"417"  => "Expectation Failed",
			"500"  => "Internal Server Error",
			"501"  => "Not Implemented",
			"502"  => "Bad Gateway",
			"503"  => "Service Unavailable",
			"504"  => "Gateway Time-out",
			"505"  => "HTTP Version not supported");

		if (isset($defaultReasons[$this->statusCode]))
		{
			return $defaultReasons[$this->statusCode];
		}

		return '';
	}    
}
// EOF