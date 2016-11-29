<?php
namespace Evoke_Test\Service;

use Evoke\Service\ShutdownHandler;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * @covers Evoke\Service\ShutdownHandler
 * @uses   Evoke\View\HTML5\MessageBox
 */
class ShutdownHandlerTest extends PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    /**
     * We can create an object.
     *
     */
    public function testCreate()
    {
        $object = new ShutdownHandler(
            'mail@example.com',
            $this->createMock('Evoke\Network\HTTP\ResponseIface'),
            false,
            $this->createMock('Evoke\View\HTML5\MessageBox'),
            $this->createMock('Evoke\Writer\WriterIface')
        );

        $this->assertInstanceOf('Evoke\Service\ShutdownHandler', $object);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage needs Error view if we are showing the error
     */
    public function testConstructionWithInvalidParameters()
    {
        $object = new ShutdownHandler(
            'a@b.com',
            $this->createMock('Evoke\Network\HTTP\ResponseIface'),
            true,
            $this->createMock('Evoke\View\HTML5\MessageBox'),
            $this->createMock('Evoke\Writer\WriterIface')
        );
    }

    /**
     * The shutdown does not display errors that would not have caused the
     * shutdown.
     */
    public function testShutdownNormal()
    {
        // Set an error handler to allow an appropriate error to be injected
        // so that we can test the shutdown handler.
        set_error_handler(
            function ($errNo, $errStr) {
                return true;
            }
        );

        // Inject a non-shutdown type error.
        trigger_error('Non shutdown type error.', E_USER_ERROR);
        restore_error_handler();

        $viewMessageBox = $this->createMock('Evoke\View\HTML5\MessageBox');
        $viewMessageBox
            ->expects($this->never())
            ->method('setTitle');
        $viewMessageBox
            ->expects($this->never())
            ->method('addContent');

        $object = new ShutdownHandler(
            'mail@example.com',
            $this->createMock('Evoke\Network\HTTP\ResponseIface'),
            false,
            $viewMessageBox,
            $this->createMock('Evoke\Writer\WriterIface')
        );
        $object->handler();
    }

    /**
     * The shutdown can display an error that is handled by it, e.g E_PARSE.
     *
     * @uses Evoke\View\HTML5\Error
     */
    public function testShutdownUsefulHandler()
    {
        // Set an error handler to allow an appropriate error to be injected
        // so that we can test the shutdown handler.
        set_error_handler(
            function ($errNo, $errStr) {
                return true;
            }
        );

        $responseIndex = 0;
        $response      = $this->createMock('Evoke\Network\HTTP\ResponseIface');
        $response
            ->expects($this->at($responseIndex++))
            ->method('setStatus')
            ->with(500);
        $response
            ->expects($this->at($responseIndex++))
            ->method('setBody')
            ->with('Writer Output');
        $response
            ->expects($this->at($responseIndex++))
            ->method('send');

        $viewErrorIndex = 0;
        $viewError      = $this->createMock('Evoke\View\HTML5\Error');
        $viewError
            ->expects($this->at($viewErrorIndex++))
            ->method('set');
        $viewError
            ->expects($this->at($viewErrorIndex++))
            ->method('get')
            ->will($this->returnValue(['div', [], 'View Error']));

        $viewMessageBoxIndex = 0;
        $viewMessageBox      = $this->createMock('Evoke\View\HTML5\MessageBox');
        $viewMessageBox
            ->expects($this->at($viewMessageBoxIndex++))
            ->method('setTitle')
            ->with('Fatal Error');
        $viewMessageBox
            ->expects($this->at($viewMessageBoxIndex++))
            ->method('addContent')
            ->with([
                'p',
                ['class' => 'Description'],
                'This is an error that we were unable to handle.  Please ' .
                'tell us any information that could help us avoid this ' .
                'error in the future.  Useful information such as the ' .
                'date, time and what you were doing when the error ' .
                'occurred should help us fix this.'
            ]);
        $viewMessageBox
            ->expects($this->at($viewMessageBoxIndex++))
            ->method('addContent')
            ->with([
                'div',
                ['class' => 'Contact'],
                'Contact: mail@example.com'
            ]);
        $viewMessageBox
            ->expects($this->at($viewMessageBoxIndex++))
            ->method('addContent')
            ->with(['div', [], 'View Error']);
        $viewMessageBox
            ->expects($this->at($viewMessageBoxIndex++))
            ->method('get')
            ->will($this->returnValue('View Message Box Output'));

        $writerIndex = 0;
        $writer      = $this->createMock('Evoke\Writer\WriterIface');
        $writer
            ->expects($this->at($writerIndex++))
            ->method('write')
            ->with('View Message Box Output');
        $writer
            ->expects($this->at($writerIndex++))
            ->method('__toString')
            ->will($this->returnValue('Writer Output'));

        $object = new ShutdownHandler('mail@example.com', $response, true, $viewMessageBox, $writer, $viewError);

        uopz_set_return('error_get_last', ['type' => E_COMPILE_ERROR]);
        $object->handler();
        uopz_unset_return('error_get_last');
        restore_error_handler();
    }
}
// EOF
