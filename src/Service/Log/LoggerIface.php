<?php
declare(strict_types = 1);
/**
 * Logger Interface
 *
 * @package Service\Log
 */
namespace Evoke\Service\Log;

use DateTime;

/**
 * Logger Interface
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Service\Log
 */
interface LoggerIface
{
    /**
     * Logs a message.
     *
     * @param DateTime $date    The DateTime for the log message.
     * @param mixed    $message The message to log.
     * @param int      $level   The level of the message.
     */
    public function log(DateTime $date, $message, $level);
}
// EOF
