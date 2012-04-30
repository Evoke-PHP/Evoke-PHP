<?php
namespace Evoke\Controller;

use Evoke\Iface;
use Evoke\HTTP\MediaType;

/** The Controller is responsible for routing the request to the correct
 *  controller method and executing this response using objects from the
 *  processing, model, data and view layers.
 */
abstract class Base
{
	/** @property $defaults
	 *  @array Default values for the controller content type and output format.
	 */
	protected $defaults;

	/** @property $mediaTypeRouterFactory
	 *  @object Media Type Router Factory
	 */
	protected $mediaTypeRouterFactory;
	
	/** @property $params
	 *  @array Parameters for the Controller.
	 */
	protected $params;
	
	/** @property $provider
	 *  @object Provider 
	 */
	protected $provider;

	/** @property $response
	 *  @object Response
	 */
	protected $response;

	/** @property $request
	 *  @object Request
	 */
	protected $request;

	/** Construct the response.
	 *  @param params                 @array  Parameters for the response.
	 *  @param provider               @object Provider object.
	 *  @param request                @object Request object.
	 *  @param response               @object Response object.
	 *  @param mediaTypeRouterFactory @object Media Type Router Factory.
	 *  @param defaults               @array  Defaults for the content type and
	 *                                        output format.
	 */
	public function __construct(
		Array               		 	   $params,
		Iface\Provider      		 	   $provider,
		Iface\HTTP\Request                 $request,
		Iface\HTTP\Response                $response,
		Iface\HTTP\MediaType\RouterFactory $mediaTypeRouterFactory,
		Array                              $defaults = array(
			'Content_Type'  => 'application/xhtml+xml',
			'Output_Format' => 'XHTML'))
	{
		$this->defaults               = $defaults;
		$this->mediaTypeRouterFactory = $mediaTypeRouterFactory;
		$this->params                 = $params;
		$this->provider				  = $provider;
		$this->request 				  = $request;
		$this->response				  = $response;
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
			trigger_error(
				'Output format: ' . $this->outputFormat . ' does not ' .
				'correspond to a known content type.  The default values of ' .
				'Output Format: ' . $this->defaults['Output_Format'] . ' and ' .
				'Content Type: ' . $this->defaults['Content_Type'] .
				' will be used.');

			// Use the defaults.
			$contentType = $this->defaults['Content_Type'];
			$outputFormat = $this->defaults['Output_Format'];
		}
		
		$this->response->setContentType($contentType);

		// Respond in the correct output format.
		$this->respond($outputFormat);
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	/** Build the Media Type router.
	 *  @return @object A standard media type router with a fallback to the
	 *                  default output format.
	 */
	protected function buildMediaTypeRouter()
	{
		return $this->mediaTypeRouterFactory->buildStandard(
			$this->defaults['Output_Format']);
	}

	/** Build the Writer object that can write the output format.
	 *  @param outputFormat @string The output format required from the writer.
	 */
	protected function buildWriter($outputFormat)
	{
		return $this->provider->make('Evoke\Writer\\' . $outputFormat);
	}

	/** Respond to the HTTP method in the correct output format.
	 *  @param outputFormat @string Output format (e.g html5, XHTML, JSON)
	 */
	protected function respond($outputFormat)
	{
		$writer = $this->buildWriter($outputFormat);
		
		// Preferably we respond using the method that matches the HTTP Request
		// method, but we allow a method of ALL to cover unhandled methods.
		$methodName = strtolower($outputFormat) .
			strtoupper($this->request->getMethod());
		$methodAll = strtolower($outputFormat) . 'ALL';
		
		if (is_callable(array($this, $methodName)))
		{
			$this->{$methodName}($writer);
		}
		elseif (is_callable(array($this, $methodAll)))
		{
			$this->{$methodAll}($writer);
		}
		else
		{
			throw new \DomainException(
				__METHOD__ . ' output format: ' . $outputFormat .
				' not handled');
		}
	}
}
// EOF
