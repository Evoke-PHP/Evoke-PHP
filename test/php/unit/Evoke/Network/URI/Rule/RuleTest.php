<?php
namespace Evoke_Test\Network\URI\Rule;

use Evoke\Network\URI\Rule\Rule,
	PHPUnit_Framework_TestCase;

class Test_Rule_Extended extends Rule
{
	public function getController($uri)
	{
		return $uri;
	}

	public function isMatch($uri)
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
	
	/*********/
	/* Tests */
	/*********/

	/**
	 * @covers Evoke\Network\URI\Rule\Rule::getParams
	 */
	public function testGetParams()
	{
		$obj = new Test_Rule_Extended();
		$this->assertSame([], $obj->getParams('Any'));
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
}
// EOF