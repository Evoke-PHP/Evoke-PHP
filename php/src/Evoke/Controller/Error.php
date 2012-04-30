<?php
namespace Evoke\Controller;

use Evoke\Iface;

class Error extends Base
{
	/** Construct the Error response.
	 *  @param provider @object Provider
	 *  @param request  @object Request
	 *  @param response @object Response
	 *  @param mediaTypeRouterFactory @object Media Type Router Factory.
	 *  @param defaults               @array  Defaults for the content type and
	 *                                        output format.
	 *  @param pageSetup              @array  Setup for page based output
	 *                                        formats.
	 */
	public function __construct(
		Iface\Provider      		  	   $provider,
		Iface\HTTP\Request  		  	   $request,
		Iface\HTTP\Response 		  	   $response,
		Iface\HTTP\MediaType\RouterFactory $mediaTypeRouterFactory,
		Array                              $defaults  = array(
			'Content_Type'  => 'application/xhtml+xml',
			'Output_Format' => 'XHTML'),
		Array                              $pageSetup = array())
	{
		// Error has no parameters, so use an empty array for it.
		parent::__construct(array(), $provider, $request, $response,
		                    $mediaTypeRouterFactory, $defaults, $pageSetup);
	}
	
	/*********************/
	/* Protected Methods */
	/*********************/
	
	protected function html5ALL(Iface\Writer\Page $writer)
	{
		$this->xhtmlALL($writer);
	}
	
	protected function jsonALL(Iface\Writer $writer)
	{
		$writer->write(array('Code'    => '500',
		                     'Title'   => 'Internal Server Error'));
	}
	
	/** Respond with the error code first.
	 *  @param outputFormat @string The output format to use.
	 */
	protected function respond($outputFormat)
	{
		$this->response->setResponseCode(500);
		parent::respond($outputFormat);
	}

	protected function textALL(Iface\Writer $writer)
	{
		$writer->write('500 Internal Server Error');
	}
	
	protected function xhtmlALL(Iface\Writer\Page $writer)
	{
		$writer->writeStart(
			array_merge($this->pageSetup,
			            array('Description' => 'Internal Server Error',
			                  'Title'       => 'Internal Server Error')));
		$this->writeMessageBoxXML($writer);
		$writer->writeEnd();
	}
	
	protected function xmlALL(Iface\Writer $writer)
	{
		$this->writeMessageBoxXML($writer);
	}

	/*******************/
	/* Private Methods */
	/*******************/
	
	/// Write a Message Box in XML showing the Not Found message.
	private function writeMessageBoxXML(Iface\Writer $writer)
	{
		$element = $this->provider->make(
			'Evoke\Element\Message\Box',
			array('Attribs' => array('class' => 'Message_Box System')));

		$writer->write(
			array('div',
			      array('class' => 'Message_Box System'),
			      array(array('div',
			                  array('class' => 'Title'),
			                  'System Error'),
			            array('div',
			                  array('class' => 'Description'),
			                  'An error has occurred. We have been notified.' .
			                  '  We will fix this as soon as possible.'))));
	}	
}
// EOF