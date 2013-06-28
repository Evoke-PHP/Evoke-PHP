<?php
namespace Evoke_Test\HTTP;

use Evoke\HTTP\Request,
	PHPUnit_Framework_TestCase;

/**
 *  @covers Evoke\HTTP\Request
 */
class RequestTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	public function providerIssetQueryParam()
	{
		return [
			'Empty' => ['Expected' => FALSE,
			            'Key'      => '',
			            'Params'   => []]];
	}
	
	public function providerIsValidAccept()
	{
		return [
			'Empty'   => ['', TRUE],
			'One'     => ['text/html; q=0.5', TRUE],
			'Two'     => ['text/html; q=0.1, text/plain', TRUE],
			'Invalid' => ['>4/yo', FALSE]];
	}

	public function providerIsValidAcceptLanguage()
	{
		return [
			'Empty'   => ['', TRUE],
			'Mixed'   => ['da, en-gb;q=0.8, en;q=0.7', TRUE],
			'One-Big' => ['Englishy-Fullsize;q=0.9', TRUE],
			'Invalid' => ['12-en', FALSE]];
	}

	public function providerParseAccept()
	{
		return [
			'Empty'   => ['Header'       => '',
			              'Parsed_Value' => []],
			'One'     => ['Header'       => 'text/html; q=0.5',
			              'Parsed_Value' =>
			              [['Params'   => [],
			                'Q_Factor' => 0.5,
			                'Subtype'  => 'html',
			                'Type'     => 'text']]],
			'Two'     => ['Header'       => 'text/html; q=0.1, text/plain',
			              'Parsed_Value' =>
			              [['Params'   => [],
			                'Q_Factor' => 1,
			                'Subtype'  => 'plain',
			                'Type'     => 'text'],
			               ['Params'   => [],
			                'Q_Factor' => 0.1,
			                'Subtype'  => 'html',
			                'Type'     => 'text']]],
			'Params'  => ['Header'       => 'audio/*; q=0.2; jim=bo; joe=no',
			              'Parsed_Value' =>
			              [['Params'   => ['jim' => 'bo', 'joe' => 'no'],
			                'Q_Factor' => 0.2,
			                'Subtype'  => '*',
			                'Type'     => 'audio']]],
			'Invalid' => ['Header'       => '>4yo',
			              'Parsed_Value' => []]];
	}

	public function providerParseAcceptLanguage()
	{
		return [
			'Empty'   => ['Header'       => '',
			              'Parsed_Value' => []],
			'Mixed'   => ['Header'       => 'da, en;q=0.7, en-gb;q=0.8',
			              'Parsed_Value' =>
			              [['Language' => 'da',
			                'Q_Factor' => 1.0],
			               ['Language' => 'en-gb',
			                'Q_Factor' => 0.8],
			               ['Language' => 'en',
			                'Q_Factor' => 0.7]]],
			'One-Big' => ['Header'       => 'Englishy-Fullsize;q=0.9',
			              'Parsed_Value' =>
			              [['Language' => 'Englishy-Fullsize',
			                'Q_Factor' => 0.9]]],
			'Invalid' => ['Header'       => '12-34',
			              'Parsed_Value' => []]];
	}
	
	/***********/
	/* Fixture */
	/***********/
	
	public function tearDown()
	{
		unset($_REQUEST);
		unset($_SERVER);
	}
	
	/*********/
	/* Tests */
	/*********/

	/**
	 * Ensure we can get the method of the request.
	 *
	 * @covers Evoke\HTTP\Request::getMethod
	 */
	public function testGetMethod()
	{
		$_SERVER['REQUEST_METHOD'] = 'ANY_VALUE';
		$object = new Request;		
		$this->assertEquals('ANY_VALUE', $object->getMethod());
	}

	/**
	 * Ensure that if the request method is not set we emit a user error and
	 * default to GET.
	 *
	 * @covers Evoke\HTTP\Request::getMethod
	 */
	public function testGetMethodNone()
	{
		$errorHandlerRun = false;
		set_error_handler(
			function () use(&$errorHandlerRun)
			{
				$errorHandlerRun = true;
			});
				
		$object = new Request;
		$this->assertEquals('GET', $object->getMethod());
		$this->assertTrue(
			$errorHandlerRun,
			'Error needs to be generated for missing HTTP Method.');
		restore_error_handler();
	}

	/**
	 * Ensure that we can get a query parameter by name.
	 *
	 * @covers Evoke\HTTP\Request::getQueryParam
	 */
	public function testGetQueryParam()
	{
		$_REQUEST = ['Test_Key' => 'Test_Val'];
		$object = new Request;
		$this->assertEquals('Test_Val', $object->getQueryParam('Test_Key'));
	}

	/**
	 * Ensure that trying to get a query parameter that isn't set throws.
	 *
	 * @covers            Evoke\HTTP\Request::getQueryParam
	 * @expectedException Exception
	 */
	public function testGetQueryParamInexistant()
	{
		$object = new Request;
		$object->getQueryParam('Inexistant');
	}

	/**
	 * Ensure that we can get all of the query parameters.
	 *
	 * @covers Evoke\HTTP\Request::getQueryParams
	 */
	public function testGetQueryParams()
	{
		$params = ['One' => 1, 'Two' => 2, 'Three' => 3];
		$_REQUEST = $params;
		$object = new Request;
		$this->assertEquals($params, $object->getQueryParams());
	}

	/**
	 * Ensure that if the query parameters aren't set then an empty array
	 * is returned.
	 *
	 * @covers Evoke\HTTP\Request::getQueryParams
	 */
	public function testGetQueryParamsNone()
	{
		$object = new Request;
		$this->assertEquals([], $object->getQueryParams());
	}
	
	/**
	 * Ensure that we can get the path of the request.
	 *
	 * @covers Evoke\HTTP\Request::getURI
	 */
	public function testGetURI()
	{
		$uri = 'http://example.com/index.php?A=1&B=2';
		$_SERVER['REQUEST_URI'] = $uri;
		$object = new Request;
		$this->assertEquals($uri, $object->getURI());
	}

	/**
	 * Ensure that we can check if a query parameter is set.
	 *
	 * @covers       Evoke\HTTP\Request::issetQueryParam
	 * @dataProvider providerIssetQueryParam
	 */
	public function testIssetQueryParam($expected, $key, $params)
	{
		$_REQUEST = $params;
		$object = new Request;
		$this->assertEquals($expected, $object->issetQueryParam($key));
	}

	/**
	 * Ensure we can validate the Accept header.
	 *
	 * @covers       Evoke\HTTP\Request::isValidAccept
	 * @dataProvider providerIsValidAccept
	 */
	public function testIsValidAccept($header, $validity)
	{
		$_SERVER = ['HTTP_ACCEPT' => $header];
		$object = new Request;
		$this->assertEquals($validity, $object->isValidAccept());
	}

	/**
	 * Ensure that we can validate the Accept-Language header.
	 *
	 * @covers Evoke\HTTP\Request::isValidAcceptLanguage
	 * @dataProvider providerIsValidAcceptLanguage
	 */
	public function testIsValidAcceptLanguage($header, $validity)
	{
		$_SERVER = ['HTTP_ACCEPT_LANGUAGE' => $header];
		$object = new Request;
		$this->assertEquals($validity, $object->isValidAcceptLanguage());
	}

	/**
	 * Ensure that we can parse an Accept header.
	 *
	 * @covers       Evoke\HTTP\Request::compareAccept
	 * @covers       Evoke\HTTP\Request::parseAccept
	 * @covers       Evoke\HTTP\Request::scoreAccept
	 * @dataProvider providerParseAccept
	 */
	public function testParseAccept($header, $parsedValue)
	{
		$_SERVER = ['HTTP_ACCEPT' => $header];
		$object = new Request;

		$this->assertEquals($parsedValue, $object->parseAccept());
	}

	/**
	 * Ensure that we can parse an Accept-Language header.
	 *
	 * @covers       Evoke\HTTP\Request::compareAcceptLanguage
	 * @covers       Evoke\HTTP\Request::parseAcceptLanguage
	 * @covers       Evoke\HTTP\Request::scoreAcceptLanguage
	 * @dataProvider providerParseAcceptLanguage
	 */
	public function testParseAcceptLanguage($header, $parsedValue)
	{
		$_SERVER = ['HTTP_ACCEPT_LANGUAGE' => $header];
		$object = new Request;

		$this->assertEquals($parsedValue, $object->parseAcceptLanguage());
	}
}
// EOF