<?php
namespace Evoke_Test\Network\URI\Rule;

use Evoke\Network\URI\Rule\Rule,
	PHPUnit_Framework_TestCase;

class Test_Rule_Extended extends Rule
{
	public function getController()
	{
		return $this->uri;
	}

	public function isMatch()
	{
		return true;
	}   
}

class RuleTest extends PHPUnit_Framework_TestCase
{
	public function providerIsAuthoritative()
	{
		return ['True'  => [true],
		        'False' => [false]];
	}

	public function providerSetURI()
	{
		return ['Empty'       => [''],
		        'Long_String' => ['this/is/a/long/Uri']];
	}

	public function providerSetURINonString()
	{
		return ['Int'    => [123],
		        'Array'  => [['this is an array']],
		        'Object' => [new \StdClass]];
	}
	
	/*********/
	/* Tests */
	/*********/

	/**
	 * @covers Evoke\Network\URI\Rule\Rule::getParams
	 */
	public function testGetParams()
	{
		$obj = new Test_Rule_Extended(true);
		$this->assertSame([], $obj->getParams());
	}
		
	/**
	 * @covers       Evoke\Network\URI\Rule\Rule::__construct
	 * @covers       Evoke\Network\URI\Rule\Rule::isAuthoritative
	 * @dataProvider providerIsAuthoritative
	 */
	public function testIsAuthoritative($auth)
	{
		$obj = new Test_Rule_Extended($auth);
		$this->assertSame($auth, $obj->isAuthoritative());
	}

	/**
	 * @covers       Evoke\Network\URI\Rule\Rule::setURI
	 * @dataProvider providerSetURI
	 */
	public function testSetURI($uri)
	{
		$obj = new Test_Rule_Extended(true);
		$obj->setURI($uri);
		$this->assertSame($uri, $obj->getController());
	}

	/**
	 * @covers                   Evoke\Network\URI\Rule\Rule::setURI
	 * @dataProvider             providerSetURINonString
	 * @expectedException        InvalidArgumentException
	 * @expectedExceptionMessage needs URI as string.
	 */
	public function testSetURINonString($uriNonString)
	{
		$obj = new Test_Rule_Extended(true);
		$obj->setURI($uriNonString);
	}
}
// EOF