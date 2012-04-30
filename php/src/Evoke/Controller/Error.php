<?php
namespace Evoke\Controller;

use Evoke\Iface;

class Error extends Base
{
	/** Construct the Error response.
	 *  @param provider @object Provider
	 *  @param request  @object Request
	 *  @param response @object Response
	 */
	public function __construct(
		Iface\Provider      		  	   $provider,
		Iface\HTTP\Request  		  	   $request,
		Iface\HTTP\Response 		  	   $response,
		Iface\HTTP\MediaType\RouterFactory $mediaTypeRouterFactory)
	{
		// Error has no parameters, so use an empty array for it.
		parent::__construct(array(), $provider, $request, $response,
		                    $mediaTypeRouterFactory);
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
		$writer->writeStart(array('CSS'         => array(),
		                          'Description' => 'Internal Server Error',
		                          'Title'       => 'Internal Server Error'));
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

		$this->writer->write(
			array('div',
			      array('class' => 'Message_Box System'),
			      array(array('div',
			                  array('class' => 'Title'),
			                  $translator->get('Error_Title')),
			            array('div',
			                  array('class' => 'Description'),
			                  $descriptionWithBreaks))));
	}	
}
// EOF