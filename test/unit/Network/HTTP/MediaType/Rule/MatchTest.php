<?php
namespace Evoke_Test\Network\HTTP\MediaType\Rule;

use Evoke\Network\HTTP\MediaType\Rule\Match;
use PHPUnit_Framework_TestCase;

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
        return [
            'matched'   =>
                [
                    'output_format' => 'DC1',
                    'match'         =>
                        [
                            'type'    => 'Text',
                            'subtype' => 'HTML'
                        ],
                    'media_type'    =>
                        [
                            'type'    => 'Text',
                            'subtype' => 'HTML'
                        ],
                    'expected'      => true
                ],
            'unmatched' =>
                [
                    'output_format' => 'DC2',
                    'match'         =>
                        [
                            'type'    => 'Text',
                            'subtype' => 'HTML'
                        ],
                    'media_type'    =>
                        [
                            'type'    => 'Text',
                            'subtype' => 'XML'
                        ],
                    'expected'      => false
                ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    public function testCreate()
    {
        $obj = new Match('OutputFormat', ['DC']);
        $this->assertInstanceOf('Evoke\Network\HTTP\MediaType\Rule\Match', $obj);
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
