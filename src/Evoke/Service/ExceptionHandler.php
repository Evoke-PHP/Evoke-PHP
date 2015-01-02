<?php
/**
 * Exception Handler
 *
 * @package Service
 */
namespace Evoke\Service;

use Evoke\Network\HTTP\ResponseIface;
use Evoke\View\HTML5\Exception;
use Evoke\View\HTML5\MessageBox;
use Evoke\Writer\WriterIface;
use InvalidArgumentException;

/**
 * Exception Handler
 *
 * The system exception handler.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Service
 */
class ExceptionHandler
{
    /**
     * Response Object.
     *
     * @var ResponseIface
     */
    protected $response;

    /**
     * Whether to display the exception.
     *
     * @var bool
     */
    protected $showException;

    /**
     * Exception view.
     *
     * @var Exception
     */
    protected $viewException;

    /**
     * MessageBox view.
     *
     * @var MessageBox
     */
    protected $viewMessageBox;

    /**
     * Writer.
     *
     * @var WriterIface
     */
    protected $writer;

    /**
     * Construct an Exception Handler object.
     *
     * @param ResponseIface $response
     * @param bool          $showException
     * @param MessageBox    $viewMessageBox
     * @param WriterIface   $writer
     * @param Exception     $viewException
     * @throws InvalidArgumentException
     * If we are showing the exception and don't provide an exception view.
     */
    public function __construct(
        ResponseIface $response,
        $showException,
        MessageBox $viewMessageBox,
        WriterIface $writer,
        Exception $viewException = null
    ) {
        if ($showException && !isset($viewException)) {
            throw new InvalidArgumentException(
                'needs Exception view if we are showing the exception.');
        }

        $this->response       = $response;
        $this->showException  = $showException;
        $this->viewException  = $viewException;
        $this->viewMessageBox = $viewMessageBox;
        $this->writer         = $writer;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Handle uncaught exceptions for the system by logging information and
     * displaying a generic notice to the user so that they are informed of an
     * error without exposing information that could be used for an attack.
     *
     * @param \Exception $uncaughtException
     * An exception that was not caught in the system.
     *
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function handler(\Exception $uncaughtException)
    {
        trigger_error($uncaughtException->getMessage(), E_USER_WARNING);
        $currentBuffer = (string)($this->writer);

        if (!empty($currentBuffer)) {
            trigger_error(
                'Buffer needs to be flushed in exception handler for ' .
                'clean error page.  Buffer was: ' . $currentBuffer,
                E_USER_WARNING);
            $this->writer->flush();
        }

        $this->viewMessageBox->addContent(
            [
                'div',
                ['class' => 'Description'],
                'The administrator has been notified.'
            ]);

        if ($this->showException) {
            $this->viewException->set($uncaughtException);
            $this->viewMessageBox->addContent($this->viewException->get());
        }

        $this->writer->writeStart();
        $this->writer->write(
            [
                'head',
                [],
                [['title', [], ['Uncaught Exception']]]
            ]);
        $this->writer->write(
            [
                'body',
                [],
                [$this->viewMessageBox->get()]
            ]);
        $this->writer->writeEnd();

        $this->response->setStatus(500);
        $this->response->setBody((string)$this->writer);
        $this->response->send();
    }
}
// EOF
