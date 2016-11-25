<?php
namespace Evoke_Test\Network\HTTP\MediaType\Rule;

use Evoke\Network\HTTP\MediaType\Rule\Exact;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\HTTP\MediaType\Rule\Exact
 * @uses   Evoke\Network\HTTP\MediaType\Rule\Match
 * @uses   Evoke\Network\HTTP\MediaType\Rule\Rule
 */
class ExactTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerIsMatch()
    {
        return [
            'matches'           =>
                [
                    'output_format'  => 'DC',
                    'match'          =>
                        [
                            'type'    => 'TEXT',
                            'subtype' => 'HTML',
                            'params'  => ['A' => '1', 'B' => '2']
                        ],
                    'ignored_fields' => ['Q_Factor'],
                    'media_type'     =>
                        [
                            'type'    => 'TEXT',
                            'subtype' => 'HTML',
                            'params'  => ['A' => '1', 'B' => '2']
                        ],
                    'expected'       => true
                ],
            'unmatched by type' =>
                [
                    'output_format'  => 'DC',
                    'match'          =>
                        [
                            'type'    => 'TEXT',
                            'subtype' => 'HTML',
                            'params'  => ['A' => 1, 'B' => 2]
                        ],
                    'ignored_fields' => ['Q_Factor'],
                    'media_type'     =>
                        [
                            'type'     => 'TEXT',
                            'subtype'  => 'HTML',
                            'q_factor' => 'Ignored',
                            'params'   => ['A' => '1', 'B' => '2']
                        ],
                    'expected'       => false
                ],
            'unmatched'         =>
                [
                    'output_format'  => 'DC',
                    'match'          =>
                        [
                            'type'    => 'TEXT',
                            'subtype' => 'HTML',
                            'params'  => ['A' => 1, 'B' => 2]
                        ],
                    'ignored_fields' => ['Q_Factor'],
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
        $obj = new Exact('OFDC', ['MDC']);
        $this->assertInstanceOf('Evoke\Network\HTTP\MediaType\Rule\Exact', $obj);
    }

    /**
     * @dataProvider providerIsMatch
     */
    public function testIsMatch($outputFormat, $match, $ignoredFields, $mediaType, $expected)
    {
        $obj = new Exact($outputFormat, $match, $ignoredFields);
        $obj->setMediaType($mediaType);
        $this->assertSame($expected, $obj->isMatch());
    }
}
// EOF
