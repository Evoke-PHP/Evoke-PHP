<?php
namespace Evoke_Test\Network\HTTP;

use Evoke\Network\HTTP\Response;
use LogicException;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Network\HTTP\Response
 */
class ResponseTest extends PHPUnit_Framework_TestCase
{
    protected static $headers;

    /***********/
    /* Fixture */
    /***********/

    /**
     * Add a header so that we can keep track of the headers set.
     *
     * @param mixed $header
     */
    public static function addHeader($header)
    {
        self::$headers[] = $header;
    }

    public function setUp()
    {
        self::$headers = [];
    }

    public function providerCreate()
    {
        return [
            'null'                => [null],
            '1.0'                 => ['1.0'],
            'hypothetical_future' => ['25.987']
        ];
    }

    /**
     * Can't create a response that doesn't match a valid HTTP spec.
     *
     * @expectedException InvalidArgumentException
     */
    public function testCantCreateInvalidHTTPResponse()
    {
        $object = new Response('A.1');
    }

    /**
     * Create a response.
     *
     * @dataProvider providerCreate
     */
    public function testCreate($httpVersion)
    {
        if (!isset($httpVersion)) {
            $object = new Response;
        } else {
            $object = new Response($httpVersion);
        }

        $this->assertInstanceOf('Evoke\Network\HTTP\Response', $object);
    }

    /******************/
    /* Data Providers */
    /******************/

    public function testBodyBeginsEmpty()
    {
        $this->replaceHeaderFunctions();
        $response = new Response;
        $response->setStatus(200);

        ob_start();
        $response->send();
        $content = ob_get_contents();
        ob_end_clean();

        $this->restoreHeaderFunctions();
        $this->assertSame('', $content);
        $this->assertSame(['HTTP/1.1 200 OK'], self::$headers);
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * We can't send if the headers have already been sent.
     *
     * @expectedException        LogicException
     * @expectedExceptionMessage Headers have already been sent
     */
    public function testCantSendAfterHeadersSent()
    {
        uopz_set_return('headers_sent', true);

        $object = new Response;
        $object->setStatus(200);

        try {
            $object->send();
        } catch (LogicException $e) {
            uopz_unset_return('headers_sent');
            throw $e;
        }
    }

    /**
     * The header fields can be set.
     */
    public function testHeaderFields()
    {
        $this->replaceHeaderFunctions();
        $response = new Response;
        $response->setStatus(301);
        $response->setHeader('Location', '/foo');
        $response->setHeader('Any', 'value');
        $response->send();
        $this->restoreHeaderFunctions();

        $this->assertSame(
            [
                'HTTP/1.1 301 Moved Permanently',
                'LOCATION: /foo',
                'ANY: value'
            ],
            self::$headers
        );
    }

    /*
     * The response body is initially blank.
     */

    /**
     * We need a status code to send a response.
     *
     * @expectedException        LogicException
     * @expectedExceptionMessage HTTP Response code must be set.
     */
    public function testNeedStatusCode()
    {
        $this->replaceHeaderFunctions();

        try {
            $response = new Response;
            $response->send();
        } catch (LogicException $e) {
            $this->restoreHeaderFunctions();
            throw $e;
        }
    }

    /**
     * We can send a response with the body.
     */
    public function testSendBody()
    {
        $this->replaceHeaderFunctions();
        $response = new Response;
        $response->setStatus(200);
        $response->setBody('This is the body');

        ob_start();
        $response->send();
        $contents = ob_get_contents();
        ob_end_clean();
        $this->restoreHeaderFunctions();

        $this->assertSame(['HTTP/1.1 200 OK'], self::$headers);
        $this->assertSame('This is the body', $contents);
    }

    /**
     * We can set the caching easily.
     */
    public function testSetCache()
    {
        $this->replaceHeaderFunctions();
        uopz_set_return('time', strtotime('1 Jan 2010 12:00 GMT'));
        $response = new Response;
        $response->setStatus(200);
        $response->setCache(1, 2, 3, 4); // 1 day, 2 hours, 3 minutes, 4 secs.
        $age = (((((1 * 24) + 2) * 60) + 3) * 60) + 4;
        $response->send();
        uopz_unset_return('time');
        $this->restoreHeaderFunctions();

        $this->assertSame(
            [
                'HTTP/1.1 200 OK',
                'PRAGMA: public',
                'CACHE-CONTROL: must-revalidate maxage=' . $age,
                'EXPIRES: Sat, 02 Jan 2010 14:03:04 GMT'
            ],
            self::$headers
        );
    }

    protected function replaceHeaderFunctions()
    {
        uopz_set_return('headers_sent', false);
        uopz_set_return('header', function($str) { \Evoke_Test\Network\HTTP\ResponseTest::addHeader($str); }, true);
    }

    protected function restoreHeaderFunctions()
    {
        uopz_unset_return('headers_sent');
        uopz_unset_return('header');
    }
}
// EOF
