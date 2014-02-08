<?php
namespace Evoke_Test\Network\HTTP\MediaType\Rule;

use Evoke\Network\HTTP\MediaType\Rule\Match,
	PHPUnit_Framework_TestCase;

class MatchTest extends PHPUnit_Framework_TestCase
{
	/******************/
	/* Data Providers */
	/******************/

	public function providerIsMatch()
	{
		return ['Matched' =>
		        ['Output_Format' => 'DC1',
		         'Match'         =>
		         ['Type' => 'Text',
		          'Subtype' => 'HTML'],
		         'Media_Type' =>
		         ['Type'    => 'Text',
		          'Subtype' => 'HTML'],
		         'Expected'      => true],
		        'Unmatched' =>
		        ['Output_Format' => 'DC2',
		         'Match'         =>
		         ['Type' => 'Text',
		          'Subtype' => 'HTML'],
		         'Media_Type' =>
		         ['Type'    => 'Text',
		          'Subtype' => 'XML'],
		         'Expected'      => false]];		        
	}
	
	/*********/
	/* Tests */
	/*********/

	/**
	 * @covers Evoke\Network\HTTP\MediaType\Rule\Match::__construct
	 */
	public function testCreate()
	{
		$obj = new Match('OutputFormat', ['DC']);
		$this->assertInstanceOf('Evoke\Network\HTTP\MediaType\Rule\Match',
		                        $obj);
	}

	/**
	 * @covers       Evoke\Network\HTTP\MediaType\Rule\Match::isMatch
	 * @dataProvider providerIsMatch
	 */
	public function testIsMatch($outputFormat, $match, $mediaType, $expected)
	{
		$obj = new Match($outputFormat, $match);
		$obj->setMediaType($mediaType);

		$this->assertSame($expected, $obj->isMatch());
	}
}
// EOF