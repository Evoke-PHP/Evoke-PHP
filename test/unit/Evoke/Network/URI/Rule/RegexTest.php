<?php
namespace Evoke_Test\Network\URI\Rule\Regex;

use Evoke\Network\URI\Rule\Regex,
	PHPUnit_Framework_TestCase;

/**
 *  @covers Evoke\Network\URI\Rule\Regex
 */
class RegexTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	public function providerGood()
	{
		return [
			'Empty_Params' => [
				'Controller' => ['Match' => '/Controller_Match/',
				                 'Replace' => 'replace'],
				'Match'      => '/URI_Match/',
				'Params'     => [],
				'Authoritative' => true]];				
	}
	
	public function providerInvalidArguments()
	{
		return [
			'Bad_Controller' => [
				'Controller' => ['M' => '/a/', 'R' => 'only one letter?'],
				'Match'      => '/match/',
				'Params'     => []],
			'Bad_Params'     => [
				'Controller' => ['Match' => '/Good/', 'Replace' => 'OK'],
				'Match'      => '/match/',
				'Params'     => [
					['Bad']]]			
			];
	}

	public function providerGetParams()
	{
		return [
			'One_Match_Out_Of_Two' => [
				'Controller'    => ['Match' => '/Any/',
				                    'Replace' => 'Whatever'],
				'Match'         => '/./',
				'Params'        => [
					['Key'   => ['Match'   => '/.*K(.{3}).*/',
					             'Replace' => '\1'],
					 'Value' => ['Match'   => '/.*V(.)(.)/',
					             'Replace' => 'h\1\2']],
					['Key'   => ['Match'   => '/.*/',
					             'Replace' => 'KeyTwo'],
					 'Value' => ['Match'   => '/NO_MATCH/',
					             'Replace' => 'any']]],
				'Authoritative' => true,
				'Uri'           => 'KOneVab',
				'Expected'      => ['One' => 'hab']]
			];
	}

	public function providerIsMatch()
	{
		return [
			'Matches'   => [
				'Controller' => ['Match'   => '/DC/',
				                 'Replace' => 'X'],
				'Match'      => '/Will_M[at]*ch/',
				'Params'     => [],
				'Uri'        => 'this/Will_Match',
				'Expected'   => true],
			'Unmatched' => [
				'Controller' => ['Match'   => '/DC/',
				                 'Replace' => 'X'],
				'Match'      => '/Wont_M[at][at]ch/',
				'Params'     => [],
				'Uri'        => 'this/Wont_MXZch',
				'Expected'   => false]
			];
	}

	/*********/
	/* Tests */
	/*********/

	/**
	 * Test that the constructor builds the expected object.
	 *
	 * @covers       \Evoke\Network\URI\Rule\Regex::__construct
	 * @dataProvider providerGood
	 */
	public function test__constructGood(
		$controller, $match, $params, $authoritative)
	{
		$object = new Regex($controller, $match, $params, $authoritative);
		$this->assertInstanceOf('Evoke\Network\URI\Rule\Regex', $object);
	}

	/**
	 * Test that Invalid Param specs to the constructor raise IAE.
	 *
	 * @covers            Evoke\Network\URI\Rule\Regex::__construct
	 * @expectedException InvalidArgumentException
	 * @dataProvider      providerInvalidArguments
	 */
	public function test__constructInvalidParamSpec(
		$controller, $match, $params, $authoritative = false)
	{
		new Regex($controller, $match, $params, $authoritative);
	}
		
	/**
	 * Test that we get the expected controller.
	 *
	 *  @depends      test__constructGood
	 *  @covers       Evoke\Network\URI\Rule\Regex::getController
	 */
	public function testGetController()
	{
		$object = new Regex(['Match'   => '/foo/',
		                     'Replace' => 'bar'],
		                    'any',
		                    []);
		$object->setURI('this/foo/isFofoo');
		
		$this->assertSame('this/bar/isFobar',
		                  $object->getController());
	}

	/**
	 * Test that we get the expected parameters.
	 *
	 * @covers       Evoke\Network\URI\Rule\Regex::getParams
	 * @depends      test__constructGood
	 * @dataProvider providerGetParams
	 */
	public function testGetParams(
		$controller, $match, $params, $authoritative, $uri, $expected)
	{
		$object = new Regex($controller, $match, $params, $authoritative);
		$object->setURI($uri);
		$this->assertSame($expected, $object->getParams());
	}

	/**
	 * Test the matches for the regex.
	 *
	 * @depends      test__constructGood
	 * @covers       Evoke\Network\URI\Rule\Regex::isMatch
	 * @dataProvider providerIsMatch
	 */
	public function testIsMatch($controller, $match, $params, $uri, $expected)
	{
		$object = new Regex($controller, $match, $params);		
		$object->setURI($uri);
		$this->assertSame($expected, $object->isMatch());
	}
}
// EOF