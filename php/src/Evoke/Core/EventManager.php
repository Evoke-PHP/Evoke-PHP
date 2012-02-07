<?php
namespace Evoke\Core;
/** \class EventManager
 *  Manage the events for the system.
 *  The Event_Manager decouples method calls between objects.  The observer
 *  pattern is used to enable objects to register for the events that they are
 *  interested in.  The event manager notifies all interested parties via their
 *  callback functions.  In this way objects do their side of the callback - one
 *  registering the need for the callback and the other implementing it.
 *
 *  Callbacks are run immediately upon event notification.
 */
class EventManager
{
	/** By default all events are critical, but if there are events that can
	 *  occur that do not care if even one observer is notified then they should
	 *  be in this list.
	 */
	private $nonCriticalEvents = array();

	/// Observers for events.
	protected $observers = array();

	public function __construct(Array $setup=array())
	{
		$setup += array('Non_Critical_Events' => array(),
		                'Observers'           => array());

		if (!is_array($setup['Non_Critical_Events']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Non_Critical_Events as array');
		}

		if (!is_array($setup['Observers']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Observers as array');
		}
    
		$this->nonCriticalEvents = $setup['Non_Critical_Events'];
		$this->observers = $setup['Observers'];
	}

	/******************/
	/* Public Methods */
	/******************/
   
	/** Connect an event to an observer callback.
	 *  This creates the event if it did not exist before.
	 *  @param name \string The event name.
	 *  @param callback \mixed Observer callback.
	 *  @param params \array Parameters to use with the callback.
	 *  @param critical \bool Does someone have to be notified of this event?
	 *  @param position \int Position to insert the callback (default end).
	 */
	public function connect(
		$name, $callback, Array $params=array(), $critical=true, $position=NULL)
	{
		if (!is_callable($callback))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' callback must be callable');
		}

		$this->create($name, $critical);
      
		$observerEntry = array('Callback' => $callback,
		                       'Params'   => $params);

		// Optionally connect the observer at a specified position.
		if (isset($position))
		{
			array_splice(
				$this->observers[$name], $position, 0, array($observerEntry));
		}
		else
		{
			$this->observers[$name][] = $observerEntry;
		}
	}

	/** Create an entry for the event, which ensures that it exists.
	 *  @param name \string The event name.
	 *  @param critical \bool Whether the event needs someone to be notified.
	 */
	public function create($name, $critical=true)
	{
		if (!isset($this->observers[$name]))
		{
			$this->observers[$name] = array();
		}

		$this->setCritical($name, $critical);
	}
   
	/** Count the observers connected to an event.
	 *  @param name \string The event name.
	 *  \return \int Number of observers connected to the event.
	 */
	public function count($name)
	{
		if (!isset($this->observers[$name]))
		{
			return 0;
		}
      
		return count($this->observers[$name]);
	}
   
	/** Disconnects an observer callback from the given event.
	 *  @param name \string The event name.
	 *  @param callback \mixed Observer callback.
	 */
	public function disconnect($name, $callback)
	{
		if (!isset($this->observers[$name]))
		{
			return;
		}
      
		foreach ($this->observers[$name] as $key => $observerCallback)
		{
			if ($callback === $observerCallback['Callback'])
			{
				unset($this->observers[$name][$key]);
				return;
			}
		}
	}

	/** Disconnect the objects from all events.
	 *  @param object \Array An array of objects to disconnect.
	 */
	public function disconnectObjects(Array $objects)
	{
		foreach ($this->observers as $eventName => $observers)
		{
			foreach ($observers as $key => $observer)
			{
				if (in_array($observer['Callback'][0], $objects))
				{
					unset($this->observers[$eventName][$key]);
				}
			}

			if (empty($this->observers[$eventName]))
			{
				unset($this->observers[$eventName]);
			}
		}
	}
   
	/** Check to see if an event name is defined.
	 *  @param name \string The event name.
	 *  \return \bool Whether the event is defined.
	 */
	public function exists($name)
	{
		return isset($this->observers[$name]);
	}
   
	/** Notifiy the observers of the event.
	 *  @param name \string The event name.
	 *  @param callParams \mixed Parameters for the call.
	 */
	public function notify($name, $callParams=array())
	{
		// Do the usual action first for performance as empty doesn't balk when
		// an array key is not even set.
		if (!empty($this->observers[$name]))
		{
			foreach ($this->observers[$name] as $observer)
			{
				$params = $this->mergeParams($observer['Params'], $callParams);
				call_user_func_array($observer['Callback'], array($params));
			}
		}
		elseif (in_array($name, $this->nonCriticalEvents))
		{
			return;
		}
		else
		{
			throw new \RuntimeException(
				__METHOD__ . ' Event: ' . var_export($name, true) .
				' needs an observer to be notified. Make sure an observer is ' .
				'notified or change the event to non-critical.');
		}
	}

	/** Set the criticality of the event.
	 *  @param name \string The event name.
	 *  @param critical \bool Whether the event should be critical.
	 */
	public function setCritical($name, $critical=true)
	{
		if ($critical == true)
		{
			$this->nonCriticalEvents = array_diff($this->nonCriticalEvents,
			                                      array($name));
		}
		else
		{
			$this->nonCriticalEvents[] = $name;
		}
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	/** Merge the parameters for a callback.
	 *  @param observerParams \array The parameters from the observer entry.
	 *  @param callParams \array The parameters from the call.
	 *  \return The parameters that will be used when calling the callback.
	 */
	protected function mergeParams($observerParams, $callParams)
	{
		return array_merge($observerParams, $callParams);
	}
}
// EOF