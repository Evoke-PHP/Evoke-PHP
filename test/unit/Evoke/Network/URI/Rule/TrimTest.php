<?php
namespace Evoke_Test\Network\URI\Rule;

use Evoke\Network\URI\Rule\Trim,
	PHPUnit_Framework_TestCase;

class TrimTest extends PHPUnit_Framework_TestCase
{
	public function providerGetController()
	{
		return ['Whitespace' => ['Characters' => " \t\n",
		                         'Expected'   => 'Now trail',
		                         'URI'        => " \t\nNow trail\n \t"],
		        'abc'        => ['Characters' => 'abc',
		                         'Expected'   => 'def',
		                         'URI'        => 'accccbdefaccacbbb'],
		        'Left_Only'  => ['Characters' => 'Z',
		                         'Expected'   => '123',
		                         'URI'        => 'Z123'],
		        'Right_Only' => ['Characters' => ' ',
		                         'Expected'   => 'Input',
		                         'URI'        => 'Input      ']
			];		        
	}

	public function providerIsMatch()
	{
		return ['Whitespace_Unmatched'         =>
		        ['Characters' => " \t\n",
		         'Expected'   => false,
		         'URI'        => "NoWhitespace"],
		        'Underscores_And_Dots_Matched' =>
		        ['Characters' => '_.',
		         'Expected'   => true,
		         'URI'        => '_abcde.'],
		        'First_Character_Match_Only'   =>
		        ['Characters' => 'A',
		         'Expected'   => true,
		         'URI'        => 'Aasfopwio'],
		        'Last_Match_Only'              =>
		        ['Characters' => 'Z',
		         'Expected'   => true,
		         'URI'        => 'InputZ'],
		        'Many_Unmatched'               =>
		        ['Characters' => 'abcdefghijklmnopABCDEFG',
		         'Expected'   => false,
		         'URI'        => 'zZyY OK zwxq']
			];		        
	}
	
	/*********/
	/* Tests */
	/*********/

	/**
	 * @covers Evoke\Network\URI\Rule\Trim::__construct
	 */
	public function testCreate()
	{
		$obj = new Trim("chars");
		$this->assertInstanceOf('Evoke\Network\URI\Rule\Trim', $obj);
	}

	/**
	 * @covers Evoke\Network\URI\Rule\Trim::getController
	 * @dataProvider providerGetController
	 */
	public function testGetController($characters, $expected, $uri)
	{
		$obj = new Trim($characters);
		$obj->setURI($uri);
		$this->assertSame($expected, $obj->getController());
	}

	/**
	 * @covers Evoke\Network\URI\Rule\Trim::isMatch
	 * @dataProvider providerIsMatch
	 */
	public function testIsMatch($characters, $expected, $uri)
	{
		$obj = new Trim($characters);
		$obj->setURI($uri);
		$this->assertSame($expected, $obj->isMatch());
	}  
}
// EOF