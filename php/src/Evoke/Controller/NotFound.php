<?php
namespace Evoke\Controller;

use Evoke\Iface;

class NotFound extends Base
{
	/** Construct the Not Found response.
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
		// Not Found has no parameters, so use an empty array for it.
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
		$writer->write(
			array('Code' => '404',
			      'Text' => 'Not Found'));
	}
	
	/** Respond with the error code first.
	 *  @param outputFormat @string The output format to use.
	 */
	protected function respond($outputFormat)
	{
		$this->response->setResponseCode(404);
		parent::respond($outputFormat);
	}

	protected function textALL(Iface\Writer $writer)
	{
		$writer->write('404 Not Found');
	}
	
	protected function xhtmlALL(Iface\Writer\Page $writer)
	{
		$writer->writeStart(array('CSS'         => array(),
		                          'Description' => 'Not Found',
		                          'Title'       => 'Page Not Found'));
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
			$element->set(
				array('Description' => 'The requested page could not be found.',
				      'Title'       => 'Not Found')));
	}	
}
// EOF