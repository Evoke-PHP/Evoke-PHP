<?php
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
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Service\Log
 */
interface LoggerIface
{
	/**
	 * Logs a message.
	 *
	 * @param DateTime The DateTime for the log message.
	 * @param mixed    The message to log.
	 * @param int      The level of the message.
	 */
	public function log(DateTime $date, $message, $level);
}
// EOF
