<?php
namespace Evoke;

interface EventManagerIface
{
	
	/** Connect an event to an observer callback.
	 *  This creates the event if it did not exist before.
	 *  @param name \string The event name.
	 *  @param callback \mixed Observer callback.
	 *  @param params \array Parameters to use with the callback.
	 *  @param critical \bool Does someone have to be notified of this event?
	 *  @param position \int Position to insert the callback (default end).
	 */
	public function connect(
		$name, $callback, Array $params=array(), $critical=true, $position=NULL);

	/** Count the observers connected to an event.
	 *  @param name \string The event name.
	 *  \return \int Number of observers connected to the event.
	 */
	public function count($name);
	
	/** Create an entry for the event, which ensures that it exists.
	 *  @param name \string The event name.
	 *  @param critical \bool Whether the event needs someone to be notified.
	 */
	public function create($name, $critical=true);

	/** Disconnects an observer callback from the given event.
	 *  @param name \string The event name.
	 *  @param callback \mixed Observer callback.
	 */
	public function disconnect($name, $callback);

	/** Check to see if an event name is defined.
	 *  @param name \string The event name.
	 *  \return \bool Whether the event is defined.
	 */
	public function exists($name);
	
	/** Notifiy the observers of the event.
	 *  @param name \string The event name.
	 *  @param callParams \mixed Parameters for the call.
	 */
	public function notify($name, $callParams=array());
	
	/** Set the criticality of the event.
	 *  @param name \string The event name.
	 *  @param critical \bool Whether the event should be critical.
	 */
	public function setCritical($name, $critical=true);
}
// EOF