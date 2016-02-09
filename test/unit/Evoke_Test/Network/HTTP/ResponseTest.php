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
            'Null'                => [null],
            '1.0'                 => ['1.0'],
            'Hypothetical_Future' => ['25.987']
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
        if (!$this->hasRunkit()) {
            $this->markTestIncomplete('PHP runkit extension is required for this test.');
            return;
        }

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
        if (!$this->hasRunkit()) {
            $this->markTestIncomplete('PHP runkit extension is required for this test.');
            return;
        }

        runkit_function_rename('header', 'TEST_SAVED_header');
        runkit_function_rename('headers_sent', 'TEST_SAVED_headers_sent');
        runkit_function_add('header', '$str', 'Evoke_Test\Network\HTTP\ResponseTest::addHeader($str);');
        runkit_function_add('headers_sent', '', 'return true;');

        $object = new Response;
        $object->setStatus(200);

        try {
            $object->send();
        } catch (LogicException $e) {
            $this->restoreHeaderFunctions();
            throw $e;
        }
    }

    /**
     * The header fields can be set.
     */
    public function testHeaderFields()
    {
        if (!$this->hasRunkit()) {
            $this->markTestIncomplete('PHP runkit extension is required for this test.');
            return;
        }

        $this->replaceHeaderFunctions();
        $response = new Response;
        $response->setStatus(301);
        $response->setHeader('Location', '/foo');
        $response->setHeader('Any', 'Value');
        $response->send();
        $this->restoreHeaderFunctions();

        $this->assertSame(
            [
                'HTTP/1.1 301 Moved Permanently',
                'LOCATION: /foo',
                'ANY: Value'
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
        if (!$this->hasRunkit()) {
            $this->markTestIncomplete('PHP runkit extension is required for this test.');
            return;
        }

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
        if (!$this->hasRunkit()) {
            $this->markTestIncomplete('PHP runkit extension is required for this test.');
            return;
        }

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
        if (!$this->hasRunkit()) {
            $this->markTestIncomplete('PHP runkit extension is required for this test.');
            return;
        }

        $this->replaceHeaderFunctions();
        runkit_function_rename('time', 'TEST_SAVED_time');
        runkit_function_add('time', '', "return strtotime('1 Jan 2010 12:00 GMT');");
        $response = new Response;
        $response->setStatus(200);
        $response->setCache(1, 2, 3, 4); // 1 day, 2 hours, 3 minutes, 4 secs.
        $age = (((((1 * 24) + 2) * 60) + 3) * 60) + 4;
        $response->send();
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
        runkit_function_remove('time');
        runkit_function_rename('TEST_SAVED_time', 'time');
    }

    /**
     * Check that the runkit extension is available.
     *
     * @return bool Whether the runkit extension is available.
     */
    protected function hasRunkit()
    {
        return function_exists('runkit_function_rename') && function_exists('runkit_function_add');
    }

    protected function replaceHeaderFunctions()
    {
        runkit_function_rename('header', 'TEST_SAVED_header');
        runkit_function_rename('headers_sent', 'TEST_SAVED_headers_sent');
        runkit_function_add('header', '$str', 'Evoke_Test\Network\HTTP\ResponseTest::addHeader($str);');
        runkit_function_add('headers_sent', '', 'return false;');
    }

    protected function restoreHeaderFunctions()
    {
        runkit_function_remove('header');
        runkit_function_remove('headers_sent');
        runkit_function_rename('TEST_SAVED_header', 'header');
        runkit_function_rename('TEST_SAVED_headers_sent', 'headers_sent');
    }
}
// EOF
