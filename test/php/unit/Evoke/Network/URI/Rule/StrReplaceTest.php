<?php
namespace Evoke_Test\Network\URI\Rule;

use Evoke\Network\URI\Rule\StrReplace,
	PHPUnit_Framework_TestCase;

class StrReplaceTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	public function providerGetController()
	{
		return ['Single'   => ['Match'       => 'foo',
		                       'Replacement' => 'bar',
		                       'URI'         => 'thisfoook',
		                       'Expected'    => 'thisbarok'],
		        'Multiple' => ['Match'       => 'a',
		                       'Replacement' => 'zow',
		                       'URI'         => 'arkansas',
		                       'Expected'    => 'zowrkzownszows']];
	}

	public function providerIsMatch()
	{
		return ['Matches'   => ['Match'       => 'match',
		                        'Replacement' => 'DC',
		                        'URI'         => 'thismatches',
		                        'Expected'    => true],
		        'Unmatched' => ['Match'       => 'NOT',
		                        'Replacement' => 'DC',
		                        'URI'         => 'notInsensitiveToCase',
		                        'Expected'    => false]];
	}

	/*********/
	/* Tests */
	/*********/

	/**
	 * @covers Evoke\Network\URI\Rule\StrReplace::__construct
	 */
	public function testCreate()
	{
		$obj = new StrReplace('Match', 'Replacement');
		$this->assertInstanceOf('Evoke\Network\URI\Rule\StrReplace', $obj);
	}

	/**
	 * @covers       Evoke\Network\URI\Rule\StrReplace::getController
	 * @dataProvider providerGetController
	 */
	public function testGetController($match, $replacement, $uri, $expected)
	{
		$obj = new StrReplace($match, $replacement);
		$obj->setURI($uri);
		$this->assertSame($expected, $obj->getController());
	}

	/**
	 * @covers       Evoke\Network\URI\Rule\StrReplace::isMatch
	 * @dataProvider providerIsMatch
	 */
	public function testIsMatch($match, $replacement, $uri, $expected)
	{
		$obj = new StrReplace($match, $replacement);
		$obj->setURI($uri);
		$this->assertSame($expected, $obj->isMatch());
	}
}
// EOF