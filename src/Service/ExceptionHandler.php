<?php
declare(strict_types = 1);
/**
 * Exception Handler
 *
 * @package Service
 */
namespace Evoke\Service;

use Evoke\Network\HTTP\ResponseIface;
use Evoke\View\ThrowableIface;
use Evoke\Writer\WriterIface;

/**
 * Exception Handler
 *
 * The system exception handler.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Service
 */
class ExceptionHandler
{
    /**
     * Response Object.
     * @var ResponseIface
     */
    protected $response;

    /**
     * Throwable view.
     * @var ThrowableIface
     */
    protected $viewThrowable;

    /**
     * Writer.
     * @var WriterIface
     */
    protected $writer;

    /**
     * Construct an Exception Handler object.
     *
     * @param ResponseIface  $response
     * @param WriterIface    $writer
     * @param ThrowableIface $viewThrowable
     */
    public function __construct(ResponseIface $response, WriterIface $writer, ThrowableIface $viewThrowable = null)
    {
        $this->response       = $response;
        $this->viewThrowable  = $viewThrowable;
        $this->writer         = $writer;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Handle uncaught exception and throwables for the system by logging information and displaying a generic notice
     * to the user so that they are informed of an error without exposing information that could be used for an attack.
     *
     * @param \Throwable $uncaught A throwable that was not caught in the system.
     *
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function handler(\Throwable $uncaught)
    {
        trigger_error($uncaught->getMessage(), E_USER_WARNING);
        $currentBuffer = (string)($this->writer);

        if (!empty($currentBuffer)) {
            trigger_error(
                'Buffer needs to be flushed in exception handler for clean error page.  Buffer was: ' . $currentBuffer,
                E_USER_WARNING
            );
            $this->writer->flush();
        }

        $this->writer->writeStart();
        $this->writer->write(['head', [], [['title', [], ['Internal Error']]]]);

        if (isset($this->viewThrowable)) {
            $this->viewThrowable->set($uncaught);
            $this->writer->write(
                [
                    'body',
                    [],
                    [
                        ['h1', [], 'Internal Error'],
                        $this->viewThrowable->get()
                    ]
                ]);
        } else {
            $this->writer->write(
                [
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
        }

        $this->writer->writeEnd();

        $this->response->setStatus(500);
        $this->response->setBody((string)$this->writer);
        $this->response->send();
    }
}
// EOF
