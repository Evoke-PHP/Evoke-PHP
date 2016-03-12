<?php
namespace Evoke_Test\Network\URI\Router\Rule\RegexSharedMatch;

use Evoke\Network\URI\Router\Rule\RegexSharedMatch;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\URI\Router\Rule\RegexSharedMatch
 * @uses   Evoke\Network\URI\Router\Rule\Rule
 */
class RegexSharedMatchTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    /**
     * Data provider that provides invalid param specs to the constructor.
     *
     * The first two parameters are match and replacement which are passed
     * correctly as a string.
     * The third parameter is the param spec which should be an array of
     * elements each with keys of 'Key' and 'Value' that have string values.
     * i.e: <pre><code>array('Key' => 'xxx', 'Value' => 'yyy')</code></pre>
     */
    public function provider__constructInvalidParamSpec()
    {
        return [
            'bad_empty_param_spec'       =>
                [
                    'match'       => 'One1',
                    'replacement' => 'One2',
                    'params'      => [[]]
                ],
            'param_spec_value_bad(bool)' =>
                [
                    'match'       => 'Two1',
                    'replacement' => 'Two2',
                    'params'      => [
                        [
                            'key'     => 'Good',
                            'novalue' => false
                        ]
                    ]
                ],
            'param_spec_key_bad(bool)'   =>
                [
                    'match'       => 'Tri1',
                    'replacement' => 'Tri2',
                    'params'      => [
                        [
                            'nokey' => false,
                            'value' => 'Good'
                        ]
                    ]
                ]
        ];
    }

    /**
     * Data provider for testGetParams.
     */
    public function providerGetParams()
    {
        return [
            'empty_param_spec'          =>
                [
                    'match'         => '/myUri/',
                    'replacement'   => 'replacement',
                    'params'        => [],
                    'authoritative' => false,
                    'uri'           => 'myUri/',
                    'expected'      => []
                ],
            'match_parameters_from_urI' =>
                [
                    'match'         => '/\/Product\/(\w+)\/(\w+)\/(\w+)\/(\d+)/',
                    'replacement'   => 'replacement',
                    'params'        => [
                        [
                            'key'   => 'Type',
                            'value' => '\1'
                        ],
                        [
                            'key'   => 'Size',
                            'value' => '\2'
                        ],
                        [
                            'key'   => '\3',
                            'value' => '\3'
                        ],
                        [
                            'key'   => 'ID',
                            'value' => '\4'
                        ]
                    ],
                    'authoritative' => false,
                    'uri'           => '/Product/Banana/Big/Yellow/123',
                    'expected'      => [
                        'Type'   => 'Banana',
                        'Size'   => 'Big',
                        'Yellow' => 'Yellow', // Test key can be regexed too.
                        'ID'     => '123'
                    ]
                ],
        ];
    }

    /**
     * Data provider for providing to the isMatch method.
     */
    public function providerIsMatch()
    {
        return [
            'match_empty_matches_empty_uri'            =>
                [
                    'match'         => '/^$/',
                    'replacement'   => 'any',
                    'params'        => [],
                    'authoritative' => false,
                    'uri'           => '',
                    'expected'      => true
                ],
            'match_something_does_not_match_empty_uri' =>
                [
                    'match'         => '/something/',
                    'replacement'   => 'good',
                    'params'        => [],
                    'authoritative' => false,
                    'uri'           => '',
                    'expected'      => false
                ],
            'match_different_from_uri'                 =>
                [
                    'match'         => '/bad/',
                    'replacement'   => 'good',
                    'params'        => [],
                    'authoritative' => false,
                    'uri'           => 'uri',
                    'expected'      => false
                ],
            'match_does_match_uri'                     =>
                [
                    'match'         => '/good/',
                    'replacement'   => 'bad',
                    'params'        => [],
                    'authoritative' => false,
                    'uri'           => 'hello/goodday',
                    'expected'      => true
                ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * Test that the constructor builds the expected object.
     */
    public function test__constructGood()
    {
        $obj = new RegexSharedMatch('str', 'str', [], true);
        $this->assertInstanceOf('Evoke\Network\URI\Router\Rule\RegexSharedMatch', $obj);
    }

    /**
     * Test that Invalid Param specs to the constructor raise IAE.
     *
     * @expectedException InvalidArgumentException
     * @dataProvider      provider__constructInvalidParamSpec
     */
    public function test__constructInvalidParamSpec($match, $replacement, Array $paramSpec, $authoritative = false)
    {
        new RegexSharedMatch($match, $replacement, $paramSpec, $authoritative);
    }

    /** Test getResponse and the private method getMappedValue.
     *
     * @depends      test__constructGood
     */
    public function testGetController()
    {
        $obj = new RegexSharedMatch('/foo/', 'bar');
        $obj->setURI('this/foo/isFofoo');
        $this->assertSame('this/bar/isFobar', $obj->getController());
    }

    /**
     * @depends      test__constructGood
     * @dataProvider providerGetParams
     */
    public function testGetParams($match, $replacement, Array $params, $authoritative, $uri, $expected)
    {
        $obj = new RegexSharedMatch($match, $replacement, $params, $authoritative);
        $obj->setURI($uri);
        $this->assertSame($expected, $obj->getParams(), 'unexpected value.');
    }

    /**
     * Test the matches for the regex.
     *
     * @depends      test__constructGood
     * @dataProvider providerIsMatch
     */
    public function testIsMatch($match, $replacement, Array $params, $authoritative, $uri, $expected)
    {
        $obj = new RegexSharedMatch($match, $replacement, $params, $authoritative);
        $obj->setURI($uri);
        $this->assertSame($expected, $obj->isMatch(), 'unexpected value.');
    }
}
// EOF
