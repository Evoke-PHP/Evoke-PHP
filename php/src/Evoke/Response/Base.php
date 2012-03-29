<?php
namespace Evoke\Response;

use Evoke\Core\Iface;
use Evoke\Core\HTTP\MediaType;

abstract class Base
{
	/** @property $Factory
	 *  \object Factory 
	 */
	protected $Factory;
	
	/** @property $params
	 *  \array Parameters for the response.
	 */
	protected $params;
	
	/** @property $Request
	 *  Request \object
	 */
	protected $Request;

	/** Construct the response.
	 *  @param params  \array  Parameters for the response.
	 *  @param Factory \object Factory object.
	 *  @param Request \object Request object.
	 */
	public function __construct(Array $params,
	                            Iface\Factory $Factory,
	                            Iface\HTTP\Request $Request)
	{
		$this->Factory = $Factory;
		$this->params  = $params;
		$this->Request = $Request;
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Execute the reponse to the given request.
	 */
	public function execute()
	{
		$MediaTypeRouter = $this->buildMediaTypeRouter();
		$outputFormat = $MediaTypeRouter->route();
		$this->setWriter($outputFormat);
		$this->respond($outputFormat);
	}

	/** Respond to the request in the correct output format.
	 *  @param outputFormat \string The output format.
	 */
	public function respond($outputFormat)
	{
		$methodName = 'respondWith' . $outputFormat;
		
		if (!is_callable(array($this, $methodName)))
		{
			throw new \DomainException(
				__METHOD__ . ' output format not handled (' . $methodName .
				' not defined).');
		}

		// Normally I hate variable references to methods, but this allows us
		// to call any response to an output format that could be defined in
		// subclasses that we are yet to know about.
		$this->$methodName();
	}
	
	/** Set the headers to show that the document should be cached. This must
	 *  come before any output is set in the document (otherwise the headers will
	 *  have already been sent).
	 *
	 *  @param days    \int The number of days to cache the document for.
	 *  @param hours   \int The number of hours to cache the document for.
	 *  @param minutes \int The number of minutes to cache the document for.
	 *  @param seconds \int The number of seconds to cache the document for.
	 */
	public function setCache($days=0, $hours=0, $minutes=0, $seconds=0)
	{
		if (headers_sent())
		{
			throw new \RuntimeException(
				__METHOD__ . ' headers have already been sent.');
		}
      
		// Calculate the offset in seconds.
		$offset = ((((($days * 24) + $hours) * 60) + $minutes) * 60) + $seconds;

		header('Pragma: public');
		header('Cache-Control: must-revalidate maxage=' . $offset);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT');
	}

	/** Set the content type for the document.
	 */
	public function setContentType($contentType)
	{
		if (headers_sent())
		{
			throw new \RuntimeException(
				__METHOD__ . ' headers have already been sent.');
		}

		header('Content-Type: ' . $contentType);
	}

	/** Set the reponse code (200 OK, 404 Not Found, etc.)
	 *  @param code \int The HTTP status code.
	 */
	public function setResponseCode($code)
	{
		if (headers_sent())
		{
			throw new \RuntimeException(
				__METHOD__ . ' headers have already been sent.');
		}

		/** http_response_code should appear in PHP 5.4, but to keep the
		 *  framework 5.3 compatible we only use it if it is callable.
		 */
		if (is_callable('http_response_code'))
		{
			http_response_code($code);
		}
		else
		{
			/// \todo Make a switch for the status text.
			header('HTTP/1.0 ' . $code . ' todo status text');	
		}
	}

	/** Set the Writer object that will write the response.
	 *  @param outputFormat \string Output format that the Writer should use.
	 */
	public function setWriter($outputFormat)
	{
		$this->Writer = $this->buildWriter($outputFormat);
	}
	
	/*********************/
	/* Protected Methods */
	/*********************/

	/** Build the Media Type router.
	 */
	protected function buildMediaTypeRouter()
	{
		$Router = new MediaType\Router($this->Request);
		$Router->addRule(
			new MediaType\Rule\Exact(array('Subtype' => '*',
			                               'Type'    => '*'),
			                         'XML'));
		return $Router;
	}

	/** Build a writer for the specified output format.
	 *  @outputFormat \string The output format required from the writer.
	 *  @return \object Writer object.
	 */
	public function buildWriter($outputFormat)
	{
		if (!is_string($outputFormat))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' outputFormat must be a string.');
		}
		
		switch (strtoupper($outputFormat))
		{
		case 'TEXT':
			return NULL;
			break;
		case 'XHTML':
		case 'XML':
			return new \Evoke\Core\Writer\XWR(
				array('XMLWriter' => new \XMLWriter()));
		default:
			throw new \DomainException(
				__METHOD__ . ' unknown output format: ' . $outputFormat);
		}
	}

	/** Get the setup for the XHTML End.
	 *  @return \array The setup for the XHTML End.
	 */
	protected function getXHTMLEndSetup()
	{
		return array();
	}

	/** Get the setup for the XHTML Head.
	 *  @return \array The setup for the XHTML Head.
	 */
	protected function getXHTMLHeadSetup()
	{
		return array('CSS' => array('/csslib/global.css',
		                            '/csslib/common.css'));
	}	
	
	/// Respond with JSON.
	protected function respondWithJSON()
	{
		throw new \BadMethodCallException(
			__METHOD__ . ' output format not handled.');
	}
	
	/// Respond with Text.
	protected function respondWithText()
	{
		throw new \BadMethodCallException(
			__METHOD__ . ' output format not handled.');
	}

	/** Respond with XHTML. As XHTML is page based we use this method to split
	 *  the implementation into Head, Content and End.
	 */
	protected function respondWithXHTML()
	{
		$this->respondWithXHTMLHead($this->getXHTMLHeadSetup());
		$this->respondWithXHTMLContent();
		$this->respondWithXHTMLEnd($this->getXHTMLEndSetup());
	}

	/// Respond with XHTML Content.
	protected function respondWithXHTMLContent()
	{
		throw new \BadMethodCallException(
			__METHOD__ . ' output format not handled.');
	}

	/** Respond with the end of the XHTML page.
	 *  @param setup \array The setup for the XHTML end.
	 */
	protected function respondWithXHTMLEnd(Array $setup)
	{
		$this->Writer->writeEnd($this->getXHTMLEndSetup());
		$this->Writer->output();
	}

	/** Respond with the XHTML Head.
	 *  @param setup \array The setup for the XHTML head.
	 */
	protected function respondWithXHTMLHead(Array $setup)
	{
		$this->Writer->writeStart($this->getXHTMLHeadSetup());
	}
	
	/// Respond with XML.
	protected function respondWithXML()
	{
		throw new \BadMethodCallException(
			__METHOD__ . ' output format not handled.');
	}
}
// EOF
