<?php
use Evoke\HTTP\URI\Rule\Regex;

/**
 *  @covers Evoke\HTTP\URI\Rule\Regex
 */
class RegexTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Test that invalid arguments to the constructor raise IAE.
	 *
	 * @covers            Evoke\HTTP\URI\Rule\Regex::__construct
	 * @expectedException InvalidArgumentException
	 * @dataProvider      provider__constructInvalidArguments
	 */
	public function test__constructInvalidArguments(
		$match, $replacement, Array $params = array(), $authoritative = false)
	{
		new Evoke\HTTP\URI\Rule\Regex(
			$match, $replacement, $params, $authoritative);
	}

	/**
	 * Test that Invalid Param specs to the constructor raise IAE.
	 *
	 * @covers            Evoke\HTTP\URI\Rule\Regex::__construct
	 * @expectedException InvalidArgumentException
	 * @dataProvider      provider__constructInvalidParamSpec
	 */
	public function test__constructInvalidParamSpec(
		$match, $replacement, Array $paramSpec, $authoritative = false)
	{
		new Evoke\HTTP\URI\Rule\Regex(
			$match, $replacement, $paramSpec, $authoritative);
	}

	/**
	 * Test that the constructor builds the expected object.
	 *
	 * @covers \Evoke\HTTP\URI\Rule\Regex::__construct
	 */
	public function test__constructGood()
	{
		$obj = new Evoke\HTTP\URI\Rule\Regex('str', 'str', array(), true);
		$this->assertInstanceOf('Evoke\HTTP\URI\Rule\Regex', $obj);
	}
		
	/** Test getResponse and the private method getMappedValue.
	 *  @depends      test__constructGood
	 *  @covers       Evoke\HTTP\URI\Rule\Regex::getClassname
	 */
	public function testGetClassname()
	{
		$obj = new Evoke\HTTP\URI\Rule\Regex('/foo/', 'bar');
		$this->assertSame('this/bar/isFobar',
		                  $obj->getClassname('this/foo/isFofoo'));
	}

	/**
	 * @depends      test__constructGood	   
	 * @covers       Evoke\HTTP\URI\Rule\Regex::getParams
	 * @dataProvider providerGetParams 
	 */
	public function testGetParams(
		$match, $replacement, Array $params, $authoritative, $uri, $expected)
	{
		$obj = new Evoke\HTTP\URI\Rule\Regex(
			$match, $replacement, $params, $authoritative);
		$this->assertSame(
			$expected, $obj->getParams($uri), 'unexpected value.');
	}

	/**
	 * Test the matches for the regex.
	 *
	 * @depends      test__constructGood
	 * @covers       Evoke\HTTP\URI\Rule\Regex::isMatch
	 * @dataProvider providerIsMatch
	 */
	public function testIsMatch(
		$match, $replacement, Array $params, $authoritative, $uri, $expected)
	{
		$obj = new Evoke\HTTP\URI\Rule\Regex(
			$match, $replacement, $params, $authoritative);
		$this->assertSame($expected, $obj->isMatch($uri), 'unexpected value.');
	}

	/******************/
	/* Data Providers */
	/******************/

	/**
	 *  Data provider that provides Invalid Arguments to the constructor.
	 *
	 *  The first two parameters should be strings to be valid.
	 */
	public function provider__constructInvalidArguments()
	{
		return array(
			'Both_Bad'   =>
			array('Match'       => array('Both Bad'),
			      'Replacement' => 12),
			'Match_Good_Replacement_Bad(Object)' =>
			array('Match'       => 'Only 1 Good',
			      'Replacement' => new stdClass()),
			'Match_Good_Replacement_Bad(Array)' =>
			array('Match'       => 'Good',
			      'Replacement' => array('Bad')),
			'Match_Bad(NULL)_Replacement_Good' =>
			array('Match'       => NULL,
			      'Replacement' => 'Replacement one good.'),
			'Match_Bad(Bool)_Replacement_Good' =>
			array('Match'       => true,
			      'Replacement' => 'Match was bad.'));
	}

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
			                               'Value' => false))),
			'Param_Spec_Key_Bad(Bool)' =>
			array('Match'       => 'Tri1',
			      'Replacement' => 'Tri2',
			      'Params'  => array(array('Key'   => false,
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
}
// EOF