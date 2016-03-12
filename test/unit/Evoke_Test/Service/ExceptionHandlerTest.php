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

    public function handleErrorByRecordingItForTest($errno, $errstr)
    {
        $this->errors[] = [$errno, $errstr];
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

    public function providerConditions()
    {
        return [
            'noflush_noshow' => [false, false],
            'noflush_doshow' => [false, true],
            'doflush_noshow' => [true, false],
            'doflush_doshow' => [true, true]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * An exception is thrown if we are trying to show the exception without a
     * view.
     *
     * @expectedException InvalidArgumentException
     */
    public function testCreateBad()
    {
        $object = new ExceptionHandler(
            $this->getMock('Evoke\Network\HTTP\ResponseIface'),
            true,
            $this->getMock('Evoke\View\HTML5\MessageBox'),
            $this->getMock('Evoke\Writer\WriterIface')
        );
    }

    /**
     * An object can be created.
     */
    public function testCreateWithView()
    {
        $object = new ExceptionHandler(
            $this->getMock('Evoke\Network\HTTP\ResponseIface'),
            true,
            $this->getMock('Evoke\View\HTML5\MessageBox'),
            $this->getMock('Evoke\Writer\WriterIface'),
            $this->getMock('Evoke\View\HTML5\Exception')
        );

        $this->assertInstanceOf('Evoke\Service\ExceptionHandler', $object);
    }

    /**
     * A view is only required if we are showing the exception.
     */
    public function testCreateWithoutView()
    {
        $object = new ExceptionHandler(
            $this->getMock('Evoke\Network\HTTP\ResponseIface'),
            false,
            $this->getMock('Evoke\View\HTML5\MessageBox'),
            $this->getMock('Evoke\Writer\WriterIface')
        );

        $this->assertInstanceOf('Evoke\Service\ExceptionHandler', $object);
    }

    /**
     * @dataProvider providerConditions
     */
    public function testHandleExceptionTriggersCorrectErrors($requiresFlush, $showException)
    {
        $exception      = new \Exception('This is it.');
        $expectedErrors = [[E_USER_WARNING, 'This is it.']];
        $response       = $this->getMock('Evoke\Network\HTTP\ResponseIface');
        $writer         = $this->getMock('Evoke\Writer\WriterIface');
        $viewException  = $this->getMock('Evoke\View\HTML5\Exception');
        $viewMessageBox = $this->getMock('Evoke\View\HTML5\MessageBox');

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
        }

        $object = new ExceptionHandler($response, $showException, $viewMessageBox, $writer, $viewException);
        $object->handler($exception);

        $this->assertSame($expectedErrors, $this->errors);
    }

    /**
     * @dataProvider providerConditions
     */
    public function testHandleException($requiresFlush, $showException)
    {
        $exception     = new \Exception('This is it.');
        $responseIndex = 0;
        $response      = $this->getMock('Evoke\Network\HTTP\ResponseIface');
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
        $writer      = $this->getMock('Evoke\Writer\WriterIface');

        if ($requiresFlush) {
            $writer
                ->expects($this->at($writerIndex++))
                ->method('__toString')
                ->with()
                ->will($this->returnValue('NotEmpty'));
            $writer
                ->expects($this->at($writerIndex++))
                ->method('flush');
        } else {
            $writer
                ->expects($this->at($writerIndex++))
                ->method('__toString')
                ->with()
                ->will($this->returnValue(''));
        }

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
                [['title', [], ['Uncaught Exception']]]
            ]);
        $writer
            ->expects($this->at($writerIndex++))
            ->method('write')
            ->with([
                'body',
                [],
                [['div', [], 'MBOX is the big wig.']]
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


        $viewExceptionIndex = 0;
        $viewException      = $this->getMock('Evoke\View\HTML5\Exception');

        if ($showException) {
            $viewException
                ->expects($this->at($viewExceptionIndex++))
                ->method('set')
                ->with($exception);
            $viewException
                ->expects($this->at($viewExceptionIndex++))
                ->method('get')
                ->will($this->returnValue(['div', [], 'Exception View Element']));
        } else {
            $viewException
                ->expects($this->never())
                ->method('set');
            $viewException
                ->expects($this->never())
                ->method('get');
        }

        $viewMessageBoxIndex = 0;
        $viewMessageBox      = $this->getMock('Evoke\View\HTML5\MessageBox');
        $viewMessageBox
            ->expects($this->at($viewMessageBoxIndex++))
            ->method('addContent')
            ->with([
                'div',
                ['class' => 'Description'],
                'The administrator has been notified.'
            ]);

        if ($showException) {
            $viewMessageBox
                ->expects($this->at($viewMessageBoxIndex++))
                ->method('addContent')
                ->with(['div', [], 'Exception View Element']);
        }

        $viewMessageBox
            ->expects($this->at($viewMessageBoxIndex++))
            ->method('get')
            ->will($this->returnValue(['div', [], 'MBOX is the big wig.']));

        $object = new ExceptionHandler($response, $showException, $viewMessageBox, $writer, $viewException);
        $object->handler($exception);
    }
}
// EOF
