<?php
namespace Evoke\Service\Log;

interface LogIface
{
	/**
	 * Add a logger to the observer list.
	 *
	 * @param Evoke\Service\Log\LoggerIface The logger to add.
	 */
	public function attach(LoggerIface $observer);

	/**
	 * Remove a logger from the observer list.
	 *
	 * @param Evoke\Service\Log\LoggerIface
	 *             The logger to remove (If there are more than one occurences
	 *             of the logger in the list then only one is removed).
	 */
	public function detach(LoggerIface $observer);

	/**
	 * Log a message by calling all of the observers in the observer list.
	 *
	 * @param mixed The message to log.
	 * @param mixed The level of the message to log.
	 */
	public function log($message, $level);
}
// EOF
