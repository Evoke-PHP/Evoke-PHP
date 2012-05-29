<?php
namespace Evoke\Controller;

use DomainException,
	Evoke\HTTP\RequestIface,
	Evoke\HTTP\ResponseIface,
	Evoke\Service\ProviderIface,
	Evoke\Writer\WriterIface;
	
/** The Controller is responsible for providing and using the correct objects
 *  from the processing, model and view layers to execute the desired request
 *  from the user.
 */
abstract class Controller
{
	/** @property $pageSetup
	 *  @array Setup for the page based output formats (XHTML, HTML5).
	 */
	protected $pageSetup;

	/** @property $params
	 *  @array Parameters for the Controller.
	 */
	protected $params;
	
	/** @property $provider
	 *  @object Provider 
	 */
	protected $provider;

	/** @property $request
	 *  @object Request
	 */
	protected $request;

	/** @property $response
	 *  @object Response
	 */
	protected $response;

	/** @property $writer
	 *  @object Writer
	 */
	protected $writer;
	
	/** Construct the Controller.
	 *  @param provider  @object Provider object.
	 *  @param request   @object Request object.
	 *  @param response  @object Response object.
	 *  @param writer    @object Writer object.
	 *  @param params    @array  Parameters.
	 *  @param pageSetup @array  Setup for page based output formats.
	 */
	public function __construct(ProviderIface $provider,
	                            RequestIface  $request,
	                            ResponseIface $response,
	                            WriterIface   $writer,
	                            Array         $params,
	                            Array         $pageSetup = array())
	{
		$this->pageSetup = $pageSetup;
		$this->params    = $params;
		$this->provider	 = $provider;
		$this->request 	 = $request;
		$this->response	 = $response;
		$this->writer    = $writer;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/** Execute the controller responding to the request method in the correct
	 *  output format.  The response method is calculate using the format:
	 *  @verbatim
	 *  <outputFormat in lowercase><method with first letter uppercase>
	 *  @endverbatim
	 *
	 *  This matches our lowerCamelCase used elsewhere for naming functions.
	 *
	 *  @param method       @string The Request method.
	 *  @param outputFormat @string The output format to use.
	 */
	public function execute($method, $outputFormat)
	{
		// Preferably we respond using the method that matches the HTTP Request
		// method, but we allow a method of All to cover unhandled methods.
		$methodName = strtolower($outputFormat) . ucfirst(strtolower($method));
		$methodAll = strtolower($outputFormat) . 'All';
		
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
			throw new DomainException(
				__METHOD__ . ' output format: ' . $outputFormat .
				' not handled');
		}

		$this->writer->output();
	}
}
// EOF