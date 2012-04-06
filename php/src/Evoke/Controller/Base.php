<?php
namespace Evoke\Controller;

use Evoke\Core\Iface;
use Evoke\Core\HTTP\MediaType;

abstract class Base
{
	/** @property $Factory
	 *  \object Factory 
	 */
	protected $Factory;
	
	/** @property $params
	 *  \array Parameters for the Controller.
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
		$methodName = 'respondWith' . $outputFormat;
		
		if (!is_callable(array($this, $methodName)))
		{
			throw new \DomainException(
				__METHOD__ . ' output format not handled (' . $methodName .
				' not defined).');
		}

		switch(strtoupper($outputFormat))
		{
		case 'HTML5':
			$contentType = 'text/html';
			break;
		case 'JSON':
			$contentType = 'application/json';
			break;
		case 'TEXT':
			$contentType = 'text/plain';
			break;
		case 'XHTML':
			$contentType = 'application/xhtml+xml';
			break;
		case 'XML':
			$contentType = 'application/xml';
			break;
		default:
			$EventManager = $this->Factory->getEventManager();
			$EventManager->notify(
				'Log',
				array('Level'   => LOG_WARNING,
				      'Message' => 'Unknown output format: ' . $outputFormat,
				      'Method'  => __METHOD__));

			// Default to XHTML output.
			$contentType = 'application/xhtml+xml';
			$outputFormat = 'XHTML';
		}
		
		$this->Writer = $this->buildWriter($outputFormat);
		$this->setContentType($contentType);

		// Perform any content agnostic initialization of the reponse.
		$this->initialize();
		// Normally I hate variable references to methods, but this allows us
		// to call any response to an output format that could be defined in
		// subclasses that we are yet to know about.
		$this->{$methodName}();
		// Perform any content agnostic finalization of the response.
		$this->finalize();
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
			                         'HTML5'));
		return $Router;
	}

	/** Build a Writer object.
	 *  @param outputFormat \string The type of output the Writer must write.
	 *  @return \object The writer object.
	 */
	protected function buildWriter($outputFormat, $setup=array())
	{
		if ($outputFormat === 'HTML5' ||
		    $outputFormat === 'XHTML' ||
		    $outputFormat === 'XML')
		{
			$setup += array('XMLWriter' => $this->Factory->get('XMLWriter'));
		}
		
		return $this->Factory->get('Evoke\Core\Writer\\' . $outputFormat,
		                           $setup);
	}

	/** Perform any finalization of the response that does not relate to a
	 *  specific content type.  This is called just after the respondWith*
	 *  methods allowing common content type agnostic processing to be grouped.
	 */
	protected function finalize()
	{
		$this->Writer->output();
	}		
	
	/** Get the XHTML setup for the page.
	 *  @return \array The XHTML Setup array.
	 */
	protected function getXHTMLSetup()
	{
		return array('CSS'         => array('/csslib/global.css',
		                                    'csslib/common.css'),
		             'Description' => 'Description',
		             'Doc_Type'    => 'HTML5',
		             'Keywords'    => '',
		             'JS'          => array(),
		             'Title'       => '');
	}

	/** Perform any initialization of the response that does not relate to a
	 *  specific content type.  This has nothing to do with construction of the
	 *  response object.  It is called just prior to the respondWith* methods
	 *  allowing response codes or other shared data to be provided accross all
	 *  content types.
	 */
	protected function initialize()
	{
		$this->setResponseCode(200);
	}

	/** Merge two XHTML setups with the second taking precedence.
	 *  @param start \array The initial XHTML setup.
	 *  @param overload \array The array that takes precedence.
	 *  @return \array The final XHTML setup with sub-arrays being merged by
	 *  value.
	 */
	protected function mergeXHTMLSetup($start, $overload)
	{
		foreach ($overload as $key => $entry)
		{
			// Arrays should be appended to with only the new elements.
			if (isset($start[$key]) && is_array($start[$key]))
			{
				$start[$key] = array_merge($start[$key],
				                           array_diff($entry, $start[$key]));
			}
			else
			{
				$start[$key] = $entry;
			}
		}

		return $start;
	}

	/** Respond with XHTML5 which is by default the same as XHTML (just with a
	 *  different writer (giving a different doctype).
	 */	
	protected function respondWithHTML5()
	{
		$this->respondWithXHTML();
	}
	
	/// Respond with JSON (Override this to provide JSON).
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
		$this->respondWithXHTMLHead();
		$this->respondWithXHTMLContent();
		$this->respondWithXHTMLEnd();
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
	protected function respondWithXHTMLEnd()
	{
		$this->Writer->writeEnd();
	}

	/** Respond with the XHTML Head.
	 *  @param setup \array The setup for the XHTML head.
	 */
	protected function respondWithXHTMLHead()
	{
		$this->Writer->writeStart($this->getXHTMLSetup());
	}

	/** Respond with XML. XML has a DTD and optionally XSLT before the content.
	 */
	protected function respondWithXML()
	{
		throw new \BadMethodCallException(
			__METHOD__ . ' output format not handled.');
	}
}
// EOF
