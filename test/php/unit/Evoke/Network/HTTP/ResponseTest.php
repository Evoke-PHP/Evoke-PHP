<?php
namespace Evoke_Test\Network\HTTP;

use Evoke\Network\HTTP\Response,
	PHPUnit_Framework_TestCase;

/**
 *  @covers Evoke\Network\HTTP\Response
 */
class ResponseTest extends PHPUnit_Framework_TestCase
{ 
	/**
	 * Ensure that a response can be constructed.
	 */	  
	public function test__constructGood()
	{
		$obj = new Response;
		$this->assertInstanceOf('Evoke\Network\HTTP\Response', $obj);

		$obj = new Response('1.0');
		$this->assertInstanceOf('Evoke\Network\HTTP\Response', $obj);

		// Test a hypothetical HTTP 25.987 future protocol.
		$obj = new Response('25.987');
		$this->assertInstanceOf('Evoke\Network\HTTP\Response', $obj);
	}

	/*
	 * Ensure that the response body is initially blank.
	 *                       
	 * @covers Evoke\Network\HTTP\Response::setBody
	 * @covers Evoke\Network\HTTP\Response::send
	 *
	public function testBodyBeginsEmpty()
	{
		// Code for coverage.
		$response = new Response;
		$response->setStatus(200);
		$response->send();
		
		// Code for actually retrieving body.
		$content = file_get_contents(
			'http://evoke_unit_test/HTTP/Response/Empty.php');
		
		$this->assertSame('', $content);
	}
	*/
}
// EOF
