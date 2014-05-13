<?php
namespace Evoke_Test\Network\HTTP\MediaType\Rule;

use Evoke\Network\HTTP\MediaType\Rule\Equivalent,
    PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\HTTP\MediaType\Rule\Equivalent
 * @uses   Evoke\Network\HTTP\MediaType\Rule\Match
 * @uses   Evoke\Network\HTTP\MediaType\Rule\Rule
 */
class EquivalentTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerIsMatch()
    {
        return ['Matches'   =>
                ['Output_Format'  => 'DC',
                 'Match'          =>
                 ['Type'    => 'TEXT',
                  'Subtype' => 'HTML',
                  'Params' => ['A' => 1, 'B' => 2]],
                 'Ignored_Fields' => ['Q_Factor'],
                 'Media_Type'     =>
                 ['Type'    => 'TEXT',
                  'Subtype' => 'HTML',
                  'Params'  => ['A' => '1', 'B' => '2']],
                 'Expected'       => true],
                'Unmatched' =>
                ['Output_Format'  => 'DC',
                 'Match'          =>
                 ['Type'    => 'TEXT',
                  'Subtype' => 'HTML',
                  'Params' => ['A' => 1, 'B' => 2]],
                 'Ignored_Fields' => ['Q_Factor'],
                 'Media_Type'     =>
                 ['Type'    => 'TEXT',
                  'Subtype' => 'XML',
                  'Params'  => ['A' => 9, 'B' => 7]],
                 'Expected'       => false]
            ];
    }

    /*********/
    /* Tests */
    /*********/

    public function testCreate()
    {
        $obj = new Equivalent('Output', ['Match']);
        $this->assertInstanceOf('Evoke\Network\HTTP\MediaType\Rule\Equivalent',
                                $obj);
    }

    /**
     * @dataProvider providerIsMatch
     */
    public function testIsMatch(
        $outputFormat, $match, $ignoredFields, $mediaType, $expected)
    {
        $obj = new Equivalent($outputFormat, $match, $ignoredFields);
        $obj->setMediaType($mediaType);
        $this->assertSame($expected, $obj->isMatch());
    }
}
// EOF