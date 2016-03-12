<?php
namespace Evoke_Test\Network\HTTP;

use Evoke\Network\HTTP\Request;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\HTTP\Request
 */
class RequestTest extends PHPUnit_Framework_TestCase
{
    protected static $savedRequest;
    protected static $savedServer;

    /***********/
    /* Fixture */
    /***********/

    public static function setUpBeforeClass()
    {
        self::$savedRequest = $_REQUEST;
        self::$savedServer  = $_SERVER;
    }

    public static function tearDownAfterClass()
    {
        $_REQUEST = self::$savedRequest;
        $_SERVER  = self::$savedServer;
    }

    public function tearDown()
    {
        unset($_REQUEST);
        unset($_SERVER);
    }

    /******************/
    /* Data Providers */
    /******************/

    public function providerIssetQueryParam()
    {
        return [
            'empty' => [
                'expected' => false,
                'key'      => '',
                'params'   => []
            ]
        ];
    }

    public function providerIsValidAccept()
    {
        return [
            'empty'   => ['', true],
            'one'     => ['text/html; q=0.5', true],
            'two'     => ['text/html; q=0.1, text/plain', true],
            'invalid' => ['>4/yo', false]
        ];
    }

    public function providerIsValidAcceptLanguage()
    {
        return [
            'empty'   => ['', true],
            'mixed'   => ['da, en-gb;q=0.8, en;q=0.7', true],
            'one-big' => ['Englishy-Fullsize;q=0.9', true],
            'invalid' => ['12-en', false]
        ];
    }

    public function providerParseAccept()
    {
        return [
            'empty'   => [
                'header'       => '',
                'parsed_value' => []
            ],
            'one'     => [
                'header'       => 'text/html; q=0.5',
                'parsed_value' =>
                    [
                        [
                            'params'   => [],
                            'q_factor' => 0.5,
                            'subtype'  => 'html',
                            'type'     => 'text'
                        ]
                    ]
            ],
            'two'     => [
                'header'       => 'text/html; q=0.1, text/plain',
                'parsed_value' =>
                    [
                        [
                            'params'   => [],
                            'q_factor' => 1.0,
                            'subtype'  => 'plain',
                            'type'     => 'text'
                        ],
                        [
                            'params'   => [],
                            'q_factor' => 0.1,
                            'subtype'  => 'html',
                            'type'     => 'text'
                        ]
                    ]
            ],
            'params'  => [
                'header'       => 'audio/*; q=0.2; jim=bo; joe=no',
                'parsed_value' =>
                    [
                        [
                            'params'   => ['jim' => 'bo', 'joe' => 'no'],
                            'q_factor' => 0.2,
                            'subtype'  => '*',
                            'type'     => 'audio'
                        ]
                    ]
            ],
            'invalid' => [
                'header'       => '>4yo',
                'parsed_value' => []
            ]
        ];
    }

    public function providerParseAcceptLanguage()
    {
        return [
            'empty'   => [
                'header'       => '',
                'parsed_value' => []
            ],
            'mixed'   => [
                'header'       => 'da, en;q=0.7, en-gb;q=0.8',
                'parsed_value' =>
                    [
                        [
                            'language' => 'da',
                            'q_factor' => 1.0
                        ],
                        [
                            'language' => 'en-gb',
                            'q_factor' => 0.8
                        ],
                        [
                            'language' => 'en',
                            'q_factor' => 0.7
                        ]
                    ]
            ],
            'One-Big' => [
                'header'       => 'Englishy-Fullsize;q=0.9',
                'parsed_value' =>
                    [
                        [
                            'language' => 'Englishy-Fullsize',
                            'q_factor' => 0.9
                        ]
                    ]
            ],
            'invalid' => [
                'header'       => '12-34',
                'parsed_value' => []
            ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * Ensure we can get the method of the request.
     */
    public function testGetMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'ANY_VALUE';
        $object                    = new Request;
        $this->assertSame('ANY_VALUE', $object->getMethod());
    }

    /**
     * Ensure that if the request method is not set we emit a user error and
     * default to GET.
     */
    public function testGetMethodNone()
    {
        $errorHandlerRun = false;
        set_error_handler(
            function () use (&$errorHandlerRun) {
                $errorHandlerRun = true;
            }
        );

        $object = new Request;
        $this->assertSame('GET', $object->getMethod());
        $this->assertTrue($errorHandlerRun, 'Error needs to be generated for missing HTTP Method.');
        restore_error_handler();
    }

    /**
     * Ensure that we can get a query parameter by name.
     */
    public function testGetQueryParam()
    {
        $_REQUEST = ['test_key' => 'test_val'];
        $object   = new Request;
        $this->assertSame('test_val', $object->getParam('test_key'));
    }

    /**
     * Ensure that trying to get a query parameter that isn't set throws.
     *
     * @expectedException Exception
     */
    public function testGetQueryParamInexistant()
    {
        $object = new Request;
        $object->getParam('inexistant');
    }

    /**
     * Ensure that we can get all of the query parameters.
     */
    public function testGetQueryParams()
    {
        $params   = ['one' => 1, 'two' => 2, 'three' => 3];
        $_REQUEST = $params;
        $object   = new Request;
        $this->assertSame($params, $object->getParams());
    }

    /**
     * Ensure that if the query parameters aren't set then an empty array
     * is returned.
     */
    public function testGetQueryParamsNone()
    {
        $object = new Request;
        $this->assertSame([], $object->getParams());
    }

    /**
     * Ensure that we can get the path of the request.
     */
    public function testGetURI()
    {
        $uri                    = 'http://example.com/index.php?A=1&B=2';
        $_SERVER['REQUEST_URI'] = $uri;
        $object                 = new Request;
        $this->assertSame($uri, $object->getURI());
    }

    /**
     * Ensure that we can check if a query parameter is set.
     *
     * @dataProvider providerIssetQueryParam
     */
    public function testIssetQueryParam($expected, $key, $params)
    {
        $_REQUEST = $params;
        $object   = new Request;
        $this->assertSame($expected, $object->issetParam($key));
    }

    /**
     * Ensure we can validate the Accept header.
     *
     * @dataProvider providerIsValidAccept
     */
    public function testIsValidAccept($header, $validity)
    {
        $_SERVER = ['HTTP_ACCEPT' => $header];
        $object  = new Request;
        $this->assertSame($validity, $object->isValidAccept());
    }

    /**
     * Ensure that we can validate the Accept-Language header.
     *
     * @dataProvider providerIsValidAcceptLanguage
     */
    public function testIsValidAcceptLanguage($header, $validity)
    {
        $_SERVER = ['HTTP_ACCEPT_LANGUAGE' => $header];
        $object  = new Request;
        $this->assertSame($validity, $object->isValidAcceptLanguage());
    }

    /**
     * Ensure that we can parse an Accept header.
     *
     * @dataProvider providerParseAccept
     */
    public function testParseAccept($header, $parsedValue)
    {
        $_SERVER = ['HTTP_ACCEPT' => $header];
        $object  = new Request;

        $this->assertSame($parsedValue, $object->parseAccept());
    }

    /**
     * Ensure that we can parse an Accept-Language header.
     *
     * @dataProvider providerParseAcceptLanguage
     */
    public function testParseAcceptLanguage($header, $parsedValue)
    {
        $_SERVER = ['HTTP_ACCEPT_LANGUAGE' => $header];
        $object  = new Request;

        $this->assertSame($parsedValue, $object->parseAcceptLanguage());
    }
}
// EOF
