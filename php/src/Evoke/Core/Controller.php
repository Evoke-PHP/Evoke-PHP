<?php
namespace Evoke\Core;

/** Controller for decoupled Hierarchical Model/View/Controller architecture.
 *  The purpose of MVC is to decouple the business logic from the user interface
 *  logic.  There are many different implementations of MVC, each with subtle
 *  differences between the responsibilities of the Model, View and Controller.
 *  It is thus important to describe the architecture used in Evoke.
 *
 *  Distinct layers of computation are the key for an MVC architecture.  This
 *  distinction allows each layer to focus on only one concern.  The layers in
 *  Evoke are:
 *  - Controller  -- Management of the objects from all layers and communication
 *                   with each layer to acheive the desired results.
 *  - Model:      -- Retrieval of data.
 *  - Processing  -- Processing of inputs.
 *  - View        -- Presentation of data.
 *
 *  Evoke differs from other MVC architectures by decoupling all of the layers
 *  within the MVC architecture.  This leads to a stronger separation of
 *  concerns.
 *
 *  The Controller uses an EventManager to decouple itself from the objects in
 *  the Model, Processing and View layers.  Any number of Model, Processing and
 *  View objects can be connected to and disconnected from the Controller.  On
 *  execution the Controller notifies the EventManager with the appropriate
 *  events to acheive the processing, retrieval and display of data.
 *
 *  Usage:
 *  \code
 *  $controller = new \Evoke\Core\Controller('Event_Manager' => $EventManager);
 *  $controller->add($Model, $Processing, $View,
 *                   $AnyAmountOfModelViewOrProcessingObjects);
 *  // Execute the controller 
 *  $controller->execute();
 *  // Remove the events from the event manager so that the same MVC triad is
 *  // not re-used later on.
 *  $controller->remove($Model, $Processing, $View,
 *                      $AnyAmountOfModelViewOrProcessingObjects);
 */
class Controller
{
	/** @param $EventManager
	 *  \object EventManager used to communicate events between the MVC layers.
	 */
	protected $EventManager;

	/** @param $data
	 *  \array of data from the Model(s) for passing to the View(s).
	 */
	private $data;

	/** @param events.
	 *  \array Event names used in the communication between MVC layers.
	 */
	protected $events;

	public function __construct(Array $setup)
	{
		$setup += array('Event_Manager' => NULL,
		                'Events'       => array(
			                'Model'      => 'Model.Notify_Data',
			                'Processing' => 'Processing.Process',
			                'View'       => 'View.Write'));

		if (!$setup['Event_Manager'] instanceof EventManager)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires EventManager');
		}

		$this->EventManager = $setup['Event_Manager'];
		$this->data = array();
		$this->events = $setup['Events'];

		// Connect the Controller to receive data from the model(s).
		$this->EventManager->connect(
			'Model.Got_Data', array($this, 'addData'));
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Add Model, Processing and View objects (or arrays of them) to the
	 *  controller.  This method takes a variable number of arguments.  All
	 *  arguments must be either objects conforming to the Model, Processing or
	 *  View interfaces or arrays filled entirely of those objects.
	 */
	public function add(/* Var Args */)
	{
		$args = func_get_args();

		foreach ($args as $arg)
		{
			if (is_array($arg))
			{
				// Add the array of objects.
				call_user_func_array(array($this, 'add'), $arg);
				continue;
			}
			
			if ($arg instanceof Iface\Model)
			{
				$this->EventManager->connect(
					$this->events['Model'],	array($arg, 'notifyData'));
			}
			elseif ($arg instanceof Iface\View)
			{
				$this->EventManager->connect(
					$this->events['View'], array($arg, 'write'));
			}
			elseif ($arg instanceof Iface\Processing)
			{
				$this->EventManager->connect(
					$this->events['Processing'], array($arg, 'process'));
			}
			else
			{
				throw new \DomainException(
					__METHOD__ . ' requires object with an interface of Model, ' .
					'Processing or View');
			}
		}
	}

	/** Add the data retrieved from the Model(s) for sending to the View(s).
	 *  @param The data to be added for later notification to the view.
	 */
	public function addData($data)
	{
		$this->data = array_merge_recursive($this->data, $data);
	}


	/** Execute the Controller.  Process any user input, retrieve data from
	 *  the Model(s) and send it to the View(s).
	 */   
	public function execute()
	{
		$this->doProcessing();
		$this->getData();
		$this->write();
		$this->resetData();
	}
   
	/** Remove Model, Processing and View objects (or arrays of them) from the
	 *  controller.  This method takes a variable number of arguments.  All
	 *  arguments must be either objects conforming to the Model, Processing or
	 *  View interfaces or arrays filled entirely of those objects.
	 */
	public function remove(/* Var Args */)
	{
		$args = func_get_args();

		foreach ($args as $arg)
		{
			if (is_array($arg))
			{
				// Remove the array of objects.
				call_user_func_array(array($this, 'remove'), $arg);
				continue;
			}
			
			if ($arg instanceof Iface\Model)
			{
				$this->EventManager->disconnect(
					$this->events['Model'],	array($arg, 'notifyData'));
			}
			elseif ($arg instanceof Iface\View)
			{
				$this->EventManager->disconnect(
					$this->events['View'], array($arg, 'write'));
			}
			elseif ($arg instanceof Iface\Processing)
			{
				$this->EventManager->disconnect(
					$this->events['Processing'], array($arg, 'process'));
			}
			else
			{
				throw new \DomainException(
					__METHOD__ . ' requires object with an interface of Model, ' .
					'Processing or View');
			}
		}
	}
	
	/*********************/
	/* Protected Methods */
	/*********************/

	/// Do the processing with the Processing objects.
	protected function doProcessing()
	{
		$this->EventManager->notify($this->events['Processing']);
	}

	/// Get the data from the Model(s).
	protected function getData()
	{
		$this->EventManager->notify($this->events['Model']);
	}
   
	/// Reset the data that is passed from the Model(s) to the View(s).
	protected function resetData()
	{
		$this->data = array();
	}

	/// Write the data with the View(s).
	protected function write()
	{
		$this->EventManager->notify($this->events['View'], $this->data);
	}
}
// EOF