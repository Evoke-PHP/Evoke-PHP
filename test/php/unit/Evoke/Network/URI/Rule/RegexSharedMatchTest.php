<?php
namespace Evoke_Test\Network\URI\Rule\RegexSharedMatch;

use Evoke\Network\URI\Rule\RegexSharedMatch,
	PHPUnit_Framework_TestCase;

/**
 *  @covers Evoke\Network\URI\Rule\RegexSharedMatch
 */
class RegexSharedMatchTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	/**
	 * Data provider that provides invalid param specs to the constructor.
	 *
	 * The first two parameters are match and replacement which are passed
	 * correctly as a string.
	 * The third parameter is the param spec which should be an array of
	 * elements each with keys of 'Key' and 'Value' that have string values.
	 * i.e: <pre><code>array('Key' => 'xxx', 'Value' => 'yyy')</code></pre>
	 */
	public function provider__constructInvalidParamSpec()
	{
		return array(
			'Bad_Empty_Param_Spec' =>
			array('Match'       => 'One1',
			      'Replacement' => 'One2',
			      'Params'  => array(array())),
			'Param_Spec_Value_Bad(Bool)' =>
			array('Match'       => 'Two1',
			      'Replacement' => 'Two2',
			      'Params'  => array(array('Key'   => 'Good',
			                               'NoValue' => false))),
			'Param_Spec_Key_Bad(Bool)' =>
			array('Match'       => 'Tri1',
			      'Replacement' => 'Tri2',
			      'Params'  => array(array('NoKey'   => false,
			                               'Value' => 'Good'))));
	}

	/**
	 * Data provider for testGetParams.
	 */
	public function providerGetParams()
	{
		return array(
			'Empty_Param_Spec' =>
			array('Match'         => '/myUri/',
			      'Replacement'   => 'replacement',
			      'Params'        => array(),
			      'Authoritative' => false,
			      'Uri'           => 'myUri/',
			      'Expected'      => array()),
			'Match_Parameters_From_URI' =>
			array('Match'         => '/\/Product\/(\w+)\/(\w+)\/(\w+)\/(\d+)/',
			      'Replacement'   => 'replacement',
			      'Params'        => array(
				      array('Key'   => 'Type',
				            'Value' => '\1'),
				      array('Key'   => 'Size',
				            'Value' => '\2'),
				      array('Key'   => '\3',
				            'Value' => '\3'),
				      array('Key'   => 'ID',
				            'Value' => '\4')),
			      'Authoritative' => false,
			      'Uri'           => '/Product/Banana/Big/Yellow/123',
			      'Expected'      => array(
				      'Type'   => 'Banana',
				      'Size'   => 'Big',
				      'Yellow' => 'Yellow', // Test key can be regexed too.
				      'ID'     => '123')),
			);
	}
	
	/**
	 * Data provider for providing to the isMatch method.
	 */
	public function providerIsMatch()
	{
		return array(
			'Match_Empty_Matches_Empty_Uri' =>
			array('Match'         => '/^$/',
			      'Replacement'   => 'any',
			      'Params'        => array(),
			      'Authoritative' => false,
			      'Uri'           => '',
			      'Expected'      => true),
			'Match_Something_Does_Not_Match_Empty_Uri' =>
			array('Match'         => '/something/',
			      'Replacement'   => 'good',
			      'Params'        => array(),
			      'Authoritative' => false,
			      'Uri'           => '',
			      'Expected'      => false),
			'Match_Different_From_Uri' =>
			array('Match'         => '/bad/',
			      'Replacement'   => 'good',
			      'Params'        => array(),
			      'Authoritative' => false,
			      'Uri'           => 'uri',
			      'Expected'      => false),
			'Match_Does_Match_Uri' =>
			array('Match'         => '/good/',
			      'Replacement'   => 'bad',
			      'Params'        => array(),
			      'Authoritative' => false,
			      'Uri'           => 'hello/goodday',
			      'Expected'      => true));
	}

	/*********/
	/* Tests */
	/*********/

	/**
	 * Test that the constructor builds the expected object.
	 *
	 * @covers \Evoke\Network\URI\Rule\RegexSharedMatch::__construct
	 */
	public function test__constructGood()
	{
		$obj = new RegexSharedMatch('str', 'str', array(), true);
		$this->assertInstanceOf('Evoke\Network\URI\Rule\RegexSharedMatch', $obj);
	}

	/**
	 * Test that Invalid Param specs to the constructor raise IAE.
	 *
	 * @covers            Evoke\Network\URI\Rule\RegexSharedMatch::__construct
	 * @expectedException InvalidArgumentException
	 * @dataProvider      provider__constructInvalidParamSpec
	 */
	public function test__constructInvalidParamSpec(
		$match, $replacement, Array $paramSpec, $authoritative = false)
	{
		new RegexSharedMatch($match, $replacement, $paramSpec, $authoritative);
	}
		
	/** Test getResponse and the private method getMappedValue.
	 *  @depends      test__constructGood
	 *  @covers       Evoke\Network\URI\Rule\RegexSharedMatch::getController
	 */
	public function testGetController()
	{
		$obj = new RegexSharedMatch('/foo/', 'bar');
		$obj->setURI('this/foo/isFofoo');
		$this->assertSame('this/bar/isFobar', $obj->getController());
	}

	/**
	 * @depends      test__constructGood	   
	 * @covers       Evoke\Network\URI\Rule\RegexSharedMatch::getParams
	 * @dataProvider providerGetParams 
	 */
	public function testGetParams(
		$match, $replacement, Array $params, $authoritative, $uri, $expected)
	{
		$obj = new RegexSharedMatch(
			$match, $replacement, $params, $authoritative);
		$obj->setURI($uri);
		$this->assertSame($expected, $obj->getParams(), 'unexpected value.');
	}

	/**
	 * Test the matches for the regex.
	 *
	 * @depends      test__constructGood
	 * @covers       Evoke\Network\URI\Rule\RegexSharedMatch::isMatch
	 * @dataProvider providerIsMatch
	 */
	public function testIsMatch(
		$match, $replacement, Array $params, $authoritative, $uri, $expected)
	{
		$obj = new RegexSharedMatch(
			$match, $replacement, $params, $authoritative);
		$obj->setURI($uri);
		$this->assertSame($expected, $obj->isMatch(), 'unexpected value.');
	}
}
// EOF