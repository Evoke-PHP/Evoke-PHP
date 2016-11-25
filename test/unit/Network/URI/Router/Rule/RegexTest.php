<?php
namespace Evoke_Test\Network\URI\Router\Rule\Regex;

use Evoke\Network\URI\Router\Rule\Regex;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\URI\Router\Rule\Regex
 * @uses   Evoke\Network\URI\Router\Rule\Rule
 */
class RegexTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerGood()
    {
        return [
            'empty_params' => [
                'controller'    => [
                    'match'   => '/controller_match/',
                    'replace' => 'replace'
                ],
                'match'         => '/uri_match/',
                'params'        => [],
                'authoritative' => true
            ]
        ];
    }

    public function providerInvalidArguments()
    {
        return [
            'bad_controller' => [
                'controller' => ['m' => '/a/', 'r' => 'only one letter?'],
                'match'      => '/match/',
                'params'     => []
            ],
            'bad_params'     => [
                'controller' => ['match' => '/good/', 'replace' => 'ok'],
                'match'      => '/match/',
                'params'     => [
                    ['Bad']
                ]
            ]
        ];
    }

    public function providerGetParams()
    {
        return [
            'one_match_out_of_two' => [
                'controller'    => [
                    'match'   => '/Any/',
                    'replace' => 'Whatever'
                ],
                'match'         => '/./',
                'params'        => [
                    [
                        'key'   => [
                            'match'   => '/.*K(.{3}).*/',
                            'replace' => '\1'
                        ],
                        'value' => [
                            'match'   => '/.*V(.)(.)/',
                            'replace' => 'h\1\2'
                        ]
                    ],
                    [
                        'key'   => [
                            'match'   => '/.*/',
                            'replace' => 'KeyTwo'
                        ],
                        'value' => [
                            'match'   => '/NO_MATCH/',
                            'replace' => 'any'
                        ]
                    ]
                ],
                'authoritative' => true,
                'uri'           => 'KOneVab',
                'expected'      => ['One' => 'hab']
            ]
        ];
    }

    public function providerIsMatch()
    {
        return [
            'matches'   => [
                'controller' => [
                    'match'   => '/DC/',
                    'replace' => 'X'
                ],
                'match'      => '/Will_M[at]*ch/',
                'params'     => [],
                'uri'        => 'this/Will_Match',
                'expected'   => true
            ],
            'unmatched' => [
                'controller' => [
                    'match'   => '/DC/',
                    'replace' => 'X'
                ],
                'match'      => '/Wont_M[at][at]ch/',
                'params'     => [],
                'uri'        => 'this/Wont_MXZch',
                'expected'   => false
            ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * Test that the constructor builds the expected object.
     *
     * @dataProvider providerGood
     */
    public function test__constructGood($controller, $match, $params, $authoritative)
    {
        $object = new Regex($controller, $match, $params, $authoritative);
        $this->assertInstanceOf('Evoke\Network\URI\Router\Rule\Regex', $object);
    }

    /**
     * Test that Invalid Param specs to the constructor raise IAE.
     *
     * @expectedException InvalidArgumentException
     * @dataProvider      providerInvalidArguments
     */
    public function test__constructInvalidParamSpec($controller, $match, $params, $authoritative = false)
    {
        new Regex($controller, $match, $params, $authoritative);
    }

    /**
     * Test that we get the expected controller.
     *
     * @depends test__constructGood
     */
    public function testGetController()
    {
        $object = new Regex(
            [
                'match'   => '/foo/',
                'replace' => 'bar'
            ],
            'any',
            []
        );
        $object->setURI('this/foo/isFofoo');

        $this->assertSame('this/bar/isFobar', $object->getController());
    }

    /**
     * Test that we get the expected parameters.
     *
     * @depends      test__constructGood
     * @dataProvider providerGetParams
     */
    public function testGetParams($controller, $match, $params, $authoritative, $uri, $expected)
    {
        $object = new Regex($controller, $match, $params, $authoritative);
        $object->setURI($uri);
        $this->assertSame($expected, $object->getParams());
    }

    /**
     * Test the matches for the regex.
     *
     * @depends      test__constructGood
     * @dataProvider providerIsMatch
     */
    public function testIsMatch($controller, $match, $params, $uri, $expected)
    {
        $object = new Regex($controller, $match, $params);
        $object->setURI($uri);
        $this->assertSame($expected, $object->isMatch());
    }
}
// EOF
