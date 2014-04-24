<?php
namespace Evoke_Test\Network\URI\Rule;

use Evoke\Network\URI\Rule\Prepend,
	PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\URI\Rule\Prepend
 * @uses   Evoke\Network\URI\Rule\Rule
 */
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

	public function testCreate()
	{
		$obj = new Prepend('Prepend_String');
		$this->assertInstanceOf('Evoke\Network\URI\Rule\Prepend', $obj);
	}

	/**
	 * @dataProvider providerGetController
	 */
	public function testGetController($prepend, $uri, $expected)
	{
		$obj = new Prepend($prepend);
		$obj->setURI($uri);
		$this->assertSame($expected, $obj->getController());
	}

	public function testIsMatch()
	{
		$obj = new Prepend('anyPrep');
		$obj->setURI('anyURI');
		$this->assertTrue($obj->isMatch());
	}
}
// EOF