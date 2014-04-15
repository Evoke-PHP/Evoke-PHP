<?php
namespace Evoke_Test\Network\HTTP;

use Evoke\Network\HTTP\Response,
	PHPUnit_Framework_TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class ResponseTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	public function providerCreate()
	{
		return [
			'Null'                => [NULL],
			'1.0'                 => ['1.0'],
			'Hypothetical_Future' => ['25.987']];
	}
	
	/*********/
	/* Tests */
	/*********/

	/**
	 * Can't create a response that doesn't match a valid HTTP spec.
	 *
	 * @covers            Evoke\Network\HTTP\Response::__construct
	 * @expectedException InvalidArgumentException
	 */
	public function testCantCreateInvalidHTTPResponse()
	{
		$object = new Response('A.1');
	}
	
	/**
	 * Create a response.
	 *
	 * @covers Evoke\Network\HTTP\Response::__construct
	 * @dataProvider providerCreate
	 */	  
	public function testCreate($httpVersion)
	{
		if (!isset($httpVersion))
		{
			$object = new Response;
		}
		else
		{
			$object = new Response($httpVersion);
		}
		
		$this->assertInstanceOf('Evoke\Network\HTTP\Response', $object);
	}

	/*
	 * The response body is initially blank.
	 *                       
	 * @covers Evoke\Network\HTTP\Response::send
	 * @covers Evoke\Network\HTTP\Response::setStatus
	 */
	public function testBodyBeginsEmpty()
	{
		$response = new Response;
		$response->setStatus(200);

		ob_start();
		$response->send();
		$content = ob_get_contents();
		ob_end_clean();

		$this->assertSame('', $content);
		$this->assertSame([], xdebug_get_headers());
	}

	/**
	 * The header fields can be set.
	 *
	 * @covers Evoke\Network\HTTP\Response::send
	 * @covers Evoke\Network\HTTP\Response::setHeader
	 * @covers Evoke\Network\HTTP\Response::setStatus
	 */
	public function testHeaderFields()
	{
		$response = new Response;
		$response->setStatus(301);
		$response->setHeader('Location', '/foo');
		$response->setHeader('Any', 'Value');
		$response->send();

		$this->assertSame(['LOCATION: /foo', 'ANY: Value'],
		                  xdebug_get_headers());
	}

	/**
	 * We need a status code to send a response.
	 *
	 * @covers            Evoke\Network\HTTP\Response::send
	 * @expectedException LogicException
	 */
	public function testNeedStatusCode()
	{
		$response = new Response;
		$response->send();
	}
	
	/**
	 * We can send a response with the body.
	 *
	 * @covers Evoke\Network\HTTP\Response::send
	 * @covers Evoke\Network\HTTP\Response::setBody
	 */
	public function testSendBody()
	{
		$response = new Response;
		$response->setStatus(200);
		$response->setBody('This is the body');

		ob_start();
		$response->send();
		$contents = ob_get_contents();
		ob_end_clean();

		$this->assertSame('This is the body', $contents);
	}
	
	/**
	 * We can set the caching easily.
	 *
	 * @covers Evoke\Network\HTTP\Response::setCache
	 */
	public function testSetCache()
	{
		$response = new Response;
		$response->setStatus(200);
		$response->setCache(1, 2, 3, 4); // One day, 2 hours, 3 minutes, 4 secs.
		$age = ((26 * 60) + 3) * 60 + 4;
		$response->send();

		$headers = xdebug_get_headers();
		
		$this->assertSame('PRAGMA: public', $headers[0]);
		$this->assertSame('CACHE-CONTROL: must-revalidate maxage=93784',
		                  $headers[1]);
		$this->assertStringStartsWith(
			'EXPIRES: ', $headers[2], 'Need expiration header.');
	}
}
// EOF
