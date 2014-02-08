<?php
namespace Evoke_Test\Network\URI\Rule;

use Evoke\Network\URI\Rule\Prepend,
	PHPUnit_Framework_TestCase;

class PrependTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	public function providerGetController()
	{
		return ['Whitespace' => ['Prepend'  => ' ',
		                         'URI'      => 'any',
		                         'Expected' => ' any'],
		        'Empty'      => ['Prepend'  => 'Prep',
		                         'URI'      => '',
		                         'Expected' => 'Prep']];
	}
	
	/*********/
	/* Tests */
	/*********/

	/**
	 * @covers Evoke\Network\URI\Rule\Prepend::__construct
	 */
	public function testCreate()
	{
		$obj = new Prepend('Prepend_String');
		$this->assertInstanceOf('Evoke\Network\URI\Rule\Prepend', $obj);
	}

	/**
	 * @covers       Evoke\Network\URI\Rule\Prepend::getController
	 * @dataProvider providerGetController
	 */
	public function testGetController($prepend, $uri, $expected)
	{
		$obj = new Prepend($prepend);
		$obj->setURI($uri);
		$this->assertSame($expected, $obj->getController());
	}

	/**
	 * @covers Evoke\Network\URI\Rule\Prepend::isMatch
	 */
	public function testIsMatch()
	{
		$obj = new Prepend('anyPrep');
		$obj->setURI('anyURI');
		$this->assertTrue($obj->isMatch());
	}
}
// EOF