<?php
namespace Evoke_Test\Network\URI\Rule;

use Evoke\Network\URI\Rule\Blank,
	PHPUnit_Framework_TestCase;

class BlankTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	public function providerIsMatch()
	{
		return ['Is_Blank'  => ['Replacement' => "Replace",
                                'Expected'    => true,
                                'URI'         => ""],
		        'Non_Blank' => ['Replacement' => 'abc',
                                'Expected'   => false,
		                        'URI'        => 'nonBlank']];
	}

	/*********/
	/* Tests */
	/*********/

	/**
	 * @covers Evoke\Network\URI\Rule\Blank::__construct
	 */
	public function testCreate()
	{
		$obj = new Blank("ReplaceText");
		$this->assertInstanceOf('Evoke\Network\URI\Rule\Blank', $obj);
	}

	/**
	 * @covers Evoke\Network\URI\Rule\Blank::getController
	 */
	public function testGetController()
	{
		$obj = new Blank('Replacement');
		$obj->setURI('');
		$this->assertSame('Replacement', $obj->getController());
	}

	/**
	 * @covers Evoke\Network\URI\Rule\Blank::isMatch
	 * @dataProvider providerIsMatch
	 */
	public function testIsMatch($replacement, $expected, $uri)
	{
		$obj = new Blank($replacement);
		$obj->setURI($uri);
		$this->assertSame($expected, $obj->isMatch());
	}  
}
// EOF