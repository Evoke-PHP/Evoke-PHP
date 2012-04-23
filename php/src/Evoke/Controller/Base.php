<?php
namespace Evoke\Controller;

use Evoke\Core\Iface;
use Evoke\Core\HTTP\MediaType;

abstract class Base
{
	/** @property $factory
	 *  \object Factory 
	 */
	protected $factory;
	
	/** @property $params
	 *  \array Parameters for the Controller.
	 */
	protected $params;
	
	/** @property $request
	 *  Request \object
	 */
	protected $request;

	/** Construct the response.
	 *  @param params   \array  Parameters for the response.
	 *  @param Factory  \object Factory object.
	 *  @param Request  \object Request object.
	 *  @param Response \object Response object.
	 */
	public function __construct(Array               $params,
	                            Iface\Factory       $factory,
	                            Iface\HTTP\Request  $request,
	                            Iface\HTTP\Response $response)
	{
		$this->factory  = $factory;
		$this->params   = $params;
		$this->request  = $request;
		$this->response = $response;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/** Execute the reponse to the given request.
	 */
	public function execute()
	{
		$mediaTypeRouter = $this->buildMediaTypeRouter();
		$outputFormat = $mediaTypeRouter->route();

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
			$eventManager = $this->factory->getEventManager();
			$eventManager->notify(
				'Log',
				array('Level'   => LOG_WARNING,
				      'Message' => 'Unknown output format: ' . $outputFormat,
				      'Method'  => __METHOD__));

			// Default to XHTML output.
			$contentType = 'application/xhtml+xml';
			$outputFormat = 'XHTML';
		}
		
		$this->writer = $this->buildWriter($outputFormat);
		$this->response->setContentType($contentType);

		// Perform any content agnostic initialization of the reponse.
		$this->initialize();

		// Respond to the method in the correct output format.
		$this->respond($this->request->getMethod(), $outputFormat);
		
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
		$router = new MediaType\Router($this->request);
		$router->addRule(
			new MediaType\Rule\Exact('HTML5',
			                         array('Subtype' => '*',
			                               'Type'    => '*')));
		return $router;
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
			$setup += array('XMLWriter' => $this->factory->get('XMLWriter'));
		}
		
		return $this->factory->get('Evoke\Core\Writer\\' . $outputFormat,
		                           $setup);
	}

	/** Provide an End the XHTML output.
	 */
	protected function endXHTML()
	{
		$this->writer->writeEnd();
	}
	
	/** Perform any finalization of the response that does not relate to a
	 *  specific content type.  This is called just after the respondWith*
	 *  methods allowing common content type agnostic processing to be grouped.
	 */
	protected function finalize()
	{
		$this->writer->output();
	}		
	
	/** Get the XHTML setup for the page.
	 *  @return \array The XHTML Setup array.
	 */
	protected function getXHTMLSetup()
	{
		return array('CSS'         => array('/csslib/global.css',
		                                    '/csslib/common.css'),
		             'Description' => 'Description',
		             'Doc_Type'    => 'HTML5',
		             'Keywords'    => '',
		             'JS'          => array(),
		             'Title'       => '');
	}

	/** Perform any initialization of the response that does not relate to a
	 *  specific content type.  This has nothing to do with construction of the
	 *  response object.  It is called just prior to the response methods
	 *  allowing response codes or other shared data to be provided accross all
	 *  content types.
	 */
	protected function initialize()
	{
		$this->response->setResponseCode(200);
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

	/** Respond to the HTTP method in the correct output format.
	 *  @param method       \string HTTP method (e.g GET, POST, DELETE)
	 *  @param outputFormat \string Output format (e.g html5, XHTML, JSON)
	 */
	protected function respond($method, $outputFormat)
	{
		// Preferably we respond using the method that matches the HTTP Request
		// method, but we allow a method of ALL to cover unhandled methods.
		$methodName = strtolower($outputFormat) . strtoupper($method);
		$methodAll = strtolower($outputFormat) . 'ALL';
		
		if (is_callable(array($this, $methodName)))
		{
			$this->{$methodName}();
		}
		elseif (is_callable(array($this, $methodAll)))
		{
			$this->{$methodAll}();
		}
		else
		{
			throw new \DomainException(
				__METHOD__ . ' output format: ' . $outputFormat .
				' not handled');
		}
	}
	
	/// Write the XHTML start.
	protected function startXHTML()
	{
		$this->writer->writeStart($this->getXHTMLSetup());
	}
	 
}
// EOF
