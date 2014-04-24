<?php
namespace Evoke_Test\Network\URI\Rule;

use Evoke\Network\URI\Rule\StrReplaceRight,
	PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\URI\Rule\StrReplaceRight
 * @uses   Evoke\Network\URI\Rule\Rule
 */
class StrReplaceRightTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	public function providerGetController()
	{
		return ['Replace_Empty' => ['Uri'         => 'uriEndPart',
		                            'Match'       => 'EndPart',
		                            'Replacement' => '',
		                            'Expected'    => 'uri'],
		        'Change_End'    => ['Uri'         => 'thisMatch',
		                            'Match'       => 'Match',
		                            'Replacement' => 'REP',
		                            'Expected'    => 'thisREP']];
	}
		
	
	public function providerMatch()
	{
		return ['Match'     => ['Uri'         => 'uriEndPart',
		                        'Match'       => 'EndPart',
		                        'Replacement' => 'DC',
		                        'Expected'    => true],
		        'No_Match'  => ['Uri'         => 'uriNoMatch',
		                        'Match'       => 'False',
		                        'Replacement' => 'DC',
		                        'Expected'    => false],
		        'Not_Right' => ['Uri'         => 'uriMatchNotAtEnd',
		                        'Match'       => 'Match',
		                        'Replacement' => 'DC',
		                        'Expected'    => false]];
	}
	
	/*********/
	/* Tests */
	/*********/

	/**
	 * Construct an object.
	 */
	public function testConstruct()
	{
		$obj = new StrReplaceRight('Match', 'Replace');
		$this->assertInstanceOf('Evoke\Network\URI\Rule\StrReplaceRight', $obj);
	}

	/**
	 * @dataProvider providerGetController
	 */
	public function testGetController($uri, $match, $replacement, $expected)
	{
		$obj = new StrReplaceRight($match, $replacement);
		$obj->setURI($uri);

		$this->assertSame($expected, $obj->getController());
	}
	
	/**
	 * @dataProvider providerMatch
	 */
	public function testMatch($uri, $match, $replacement, $expected)
	{
		$obj = new StrReplaceRight($match, $replacement);
		$obj->setURI($uri);
		
		$this->assertSame($expected, $obj->isMatch());
	}
}
// EOF