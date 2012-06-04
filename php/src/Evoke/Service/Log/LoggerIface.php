<?php
namespace Evoke\Service\Log;

use DateTime;

interface LoggerIface
{
	/** Logs a message.
	 *  @param date    @object The DateTime for the log message.
	 *  @param message @array  The message to log.
	 *  @param level   @int    The level of the message.
	 */
	public function log(DateTime $date, $message, $level);
}
// EOF
