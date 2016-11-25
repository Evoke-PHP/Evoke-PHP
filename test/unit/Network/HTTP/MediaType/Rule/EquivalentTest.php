<?php
namespace Evoke_Test\Network\HTTP\MediaType\Rule;

use Evoke\Network\HTTP\MediaType\Rule\Equivalent;
use PHPUnit_Framework_TestCase;

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
        return [
            'matches'   =>
                [
                    'output_format'  => 'DC',
                    'match'          =>
                        [
                            'type'    => 'TEXT',
                            'subtype' => 'HTML',
                            'params'  => ['A' => 1, 'B' => 2]
                        ],
                    'ignored_fields' => ['q_factor'],
                    'media_type'     =>
                        [
                            'type'    => 'TEXT',
                            'subtype' => 'HTML',
                            'params'  => ['A' => '1', 'B' => '2']
                        ],
                    'expected'       => true
                ],
            'unmatched' =>
                [
                    'output_format'  => 'DC',
                    'match'          =>
                        [
                            'type'    => 'TEXT',
                            'subtype' => 'HTML',
                            'params'  => ['A' => 1, 'B' => 2]
                        ],
                    'ignored_fields' => ['q_factor'],
                    'media_type'     =>
                        [
                            'type'    => 'TEXT',
                            'subtype' => 'XML',
                            'params'  => ['A' => 9, 'B' => 7]
                        ],
                    'expected'       => false
                ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    public function testCreate()
    {
        $obj = new Equivalent('Output', ['Match']);
        $this->assertInstanceOf('Evoke\Network\HTTP\MediaType\Rule\Equivalent', $obj);
    }

    /**
     * @dataProvider providerIsMatch
     */
    public function testIsMatch($outputFormat, $match, $ignoredFields, $mediaType, $expected)
    {
        $obj = new Equivalent($outputFormat, $match, $ignoredFields);
        $obj->setMediaType($mediaType);
        $this->assertSame($expected, $obj->isMatch());
    }
}
// EOF
