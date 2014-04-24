<?php
namespace Evoke_Test\Network\HTTP\MediaType\Rule;

use Evoke\Network\HTTP\MediaType\Rule\Match,
	PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\HTTP\MediaType\Rule\Match
 * @uses   Evoke\Network\HTTP\MediaType\Rule\Rule
 */
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

	public function testCreate()
	{
		$obj = new Match('OutputFormat', ['DC']);
		$this->assertInstanceOf('Evoke\Network\HTTP\MediaType\Rule\Match',
		                        $obj);
	}

	/**
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