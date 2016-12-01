<?php
namespace Evoke_Test\Service;

use Evoke\Service\ExceptionHandler;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Service\ExceptionHandler
 * @uses   Evoke\View\HTML5\MessageBox
 */
class ExceptionHandlerTest extends PHPUnit_Framework_TestCase
{
    protected $errors = [];
    protected $savedErrorReporting;

    /***********/
    /* Fixture */
    /***********/

    public function handleErrorByRecordingItForTest(int $errNo, string $errStr)
    {
        $this->errors[] = [$errNo, $errStr];
        return true;
    }

    public function setUp()
    {
        $this->errors              = [];
        $this->savedErrorReporting = set_error_handler([$this, 'handleErrorByRecordingItForTest']);
    }

    public function tearDown()
    {
        restore_error_handler();
    }

    /******************/
    /* Data Providers */
    /******************/

    public function providerFlushBufferIfNotEmpty()
    {
        return [
            'noflush' => [false],
            'doflush' => [true]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * An object can be created.
     */
    public function testCreateWithView()
    {
        $object = new ExceptionHandler(
            $this->createMock('Evoke\Network\HTTP\ResponseIface'),
            $this->createMock('Evoke\Writer\WriterIface'),
            $this->createMock('Evoke\View\ThrowableIface')
        );

        $this->assertInstanceOf('Evoke\Service\ExceptionHandler', $object);
    }

    /**
     * A view is only required if we are showing the exception.
     */
    public function testCreateWithoutView()
    {
        $object = new ExceptionHandler(
            $this->createMock('Evoke\Network\HTTP\ResponseIface'),
            $this->createMock('Evoke\Writer\WriterIface'),
            null
        );

        $this->assertInstanceOf('Evoke\Service\ExceptionHandler', $object);
    }

    /**
     * @dataProvider providerFlushBufferIfNotEmpty
     */
    public function testFlushesBufferIfNotEmpty($requiresFlush)
    {
        $exception      = new \Exception('This is it.');
        $expectedErrors = [[E_USER_WARNING, 'This is it.']];
        $response       = $this->createMock('Evoke\Network\HTTP\ResponseIface');
        $writer         = $this->createMock('Evoke\Writer\WriterIface');

        if ($requiresFlush) {
            $writer
                ->expects($this->at(0))
                ->method('__toString')
                ->will($this->returnValue('NOT_EMPTY'));

            $expectedErrors[] =
                [
                    E_USER_WARNING,
                    'Buffer needs to be flushed in exception handler for ' .
                    'clean error page.  Buffer was: NOT_EMPTY'
                ];
        } else {
            $writer
                ->expects($this->at(0))
                ->method('__toString')
                ->with()
                ->will($this->returnValue(''));
        }

        $object = new ExceptionHandler($response, $writer);
        $object->handler($exception);

        $this->assertSame($expectedErrors, $this->errors);
    }

    public function testHandleExceptionShowingThrowable()
    {
        $exception     = new \Exception('This is it.');
        $responseIndex = 0;
        $response      = $this->createMock('Evoke\Network\HTTP\ResponseIface');
        $response
            ->expects($this->at($responseIndex++))
            ->method('setStatus')
            ->with(500);
        $response
            ->expects($this->at($responseIndex++))
            ->method('setBody')
            ->with('whatever the writer says goes: ' . __METHOD__);
        $response
            ->expects($this->at($responseIndex))
            ->method('send');

        $writerIndex = 0;
        $writer      = $this->createMock('Evoke\Writer\WriterIface');
        $writer
            ->expects($this->at($writerIndex++))
            ->method('__toString')
            ->with()
            ->will($this->returnValue(''));
        $writer
            ->expects($this->at($writerIndex++))
            ->method('writeStart')
            ->with();
        $writer
            ->expects($this->at($writerIndex++))
            ->method('write')
            ->with([
                'head',
                [],
                [['title', [], ['Internal Error']]]
            ]);
        $writer
            ->expects($this->at($writerIndex++))
            ->method('write')
            ->with([
                'body',
                [],
                [
                    ['h1', [], 'Internal Error'],
                    [
                        'div',
                        [],
                        'Throwable View'
                    ]
                ]
            ]);
        $writer
            ->expects($this->at($writerIndex++))
            ->method('writeEnd')
            ->with();
        $writer
            ->expects($this->at($writerIndex++))
            ->method('__toString')
            ->with()
            ->will($this->returnValue('whatever the writer says goes: ' . __METHOD__));

        $viewThrowableIndex = 0;
        $viewThrowable      = $this->createMock('Evoke\View\ThrowableIface');
        $viewThrowable
            ->expects($this->at($viewThrowableIndex++))
            ->method('set')
            ->with($exception);
        $viewThrowable
            ->expects($this->at($viewThrowableIndex++))
            ->method('get')
            ->will($this->returnValue(['div', [], 'Throwable View']));

        $object = new ExceptionHandler($response, $writer, $viewThrowable);
        $object->handler($exception);
    }

    public function testHandleExceptionWithoutShowingThrowable()
    {
        $exception     = new \Exception('This is it.');
        $responseIndex = 0;
        $response      = $this->createMock('Evoke\Network\HTTP\ResponseIface');
        $response
            ->expects($this->at($responseIndex++))
            ->method('setStatus')
            ->with(500);
        $response
            ->expects($this->at($responseIndex++))
            ->method('setBody')
            ->with('whatever the writer says goes.');
        $response
            ->expects($this->at($responseIndex++))
            ->method('send');

        $writerIndex = 0;
        $writer      = $this->createMock('Evoke\Writer\WriterIface');
        $writer
            ->expects($this->at($writerIndex++))
            ->method('__toString')
            ->with()
            ->will($this->returnValue(''));
        $writer
            ->expects($this->at($writerIndex++))
            ->method('writeStart')
            ->with();
        $writer
            ->expects($this->at($writerIndex++))
            ->method('write')
            ->with([
                'head',
                [],
                [['title', [], ['Internal Error']]]
            ]);
        $writer
            ->expects($this->at($writerIndex++))
            ->method('write')
            ->with([
                'body',
                [],
                [
                    ['h1', [], 'Internal Error'],
                    [
                        'p',
                        [],
                        'We are sorry about this error, the administrator has been notified and we will fix this issue as soon as possible.  Please contact us for more information.'
                    ]
                ]
            ]);
        $writer
            ->expects($this->at($writerIndex++))
            ->method('writeEnd')
            ->with();
        $writer
            ->expects($this->at($writerIndex++))
            ->method('__toString')
            ->with()
            ->will($this->returnValue('whatever the writer says goes.'));

        $object = new ExceptionHandler($response, $writer);
        $object->handler($exception);
    }
}
// EOF
