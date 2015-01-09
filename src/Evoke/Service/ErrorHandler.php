<?php
/**
 * Error Handler
 *
 * @package Service
 */
namespace Evoke\Service;

use ErrorException;
use Evoke\Service\Log\LoggingIface;

/**
 * Error Handler
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Service
 */
class ErrorHandler
{
    /**
     * Logging object.
     * @var LoggingIface
     */
    protected $logging;

    /**
     * Whether to stop the standard error handler from running after handling the error here.
     * @var bool
     */
    protected $suppressErrorHandler;

    /**
     * Construct a system error handler.
     *
     * @param LoggingIface $logging
     * @param bool         $suppressErrorHandler Whether to stop the standard error handler from running after handling
     *                                           the error here.
     */
    public function __construct(LoggingIface $logging, $suppressErrorHandler = false)
    {
        $this->suppressErrorHandler = $suppressErrorHandler;
        $this->logging              = $logging;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * System Error Handler to log error messages.
     *
     * @param int    $errNo
     * @param string $errStr
     * @param string $errFile
     * @param int    $errLine
     * @param array  $errContext
     * @return bool Whether the default system error handler should be suppressed.
     * @throws ErrorException If the error is recoverable.
     */
    public function handler($errNo, $errStr, $errFile, $errLine, Array $errContext)
    {
        // If the error code should not be reported return.
        if (!(error_reporting() & $errNo)) {
            // Do not allow PHP to report them either as PHP is telling us that
            // they should not be reported.
            return true;
        }

        $message = $errStr . ' in ' . $errFile . ' on ' . $errLine;

        if (!empty($errContext)) {
            $message .= ' context: ' . print_r($errContext, true);
        }

        $this->logging->log($message, $errNo);

        // The easiest way to recover from a recoverable error is by handling an exception.  This ensures the problem is
        // addressed before any related code fails horribly due to unexpected values.
        if ($errNo === E_RECOVERABLE_ERROR) {
            throw new ErrorException($errStr, 0, $errNo, $errFile, $errLine);
        }

        // The return value determines whether the standard error handler is suppressed or not.
        return $this->suppressErrorHandler;
    }
}
// EOF
