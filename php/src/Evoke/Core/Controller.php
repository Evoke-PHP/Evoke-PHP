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
 *  After the controller is executed any Model, Processing and View objects that
 *  were connected to it (in the event manager) are disconnected.
 *
 *  Usage:
 *  \code
 *  $controller = new \Evoke\Core\Controller('Event_Manager' => $EventManager);
 *  $controller->connect($Model, $Processing, $View,
 *                       $AnyAmountOfModelViewOrProcessingObjects);
 *  // Execute the controller 
 *  $controller->execute();
 *  \endcode
 */
class Controller
{
	/** @property $connectedModels
	 *  \array of Model objects connected to the controller.
	 */
	protected $connectedModels;

	/** @property $connectedProcessing
	 *  \array of Processing objects connected to the controller.
	 */
	protected $connectedProcessing;

	/** @property $connectedViews
	 *  \array of View objects connected to the controller.
	 */
	protected $connectedViews;
	
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

	/** Construct the controller.
	 *  @param EventManager \object The event manager object.
	 *  @param events \array The event names to trigger processing in each layer.
	 */
	public function __construct(
		EventManager $EventManager,
		Array        $events=array('Model'      => 'Model.Notify_Data',
		                           'Processing' => 'Processing.Process',
		                           'View'       => 'View.Write'))
	{
		$this->connectedModels         = array();
		$this->connectedProcessing     = array();
		$this->connectedViews          = array();		
		$this->data                    = array();
		$this->EventManager            = $EventManager;
		$this->events                  = $events;

		// Connect the Controller to receive data from the model(s).
		$this->EventManager->connect(
			'Model.Got_Data', array($this, 'addData'));
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Add the data retrieved from the Model(s) for sending to the View(s).
	 *  @param The data to be added for later notification to the view.
	 */
	public function addData($data)
	{
		$this->data = array_merge_recursive($this->data, $data);
	}

	/** Connect Model, Processing and View objects (or arrays of them) to the
	 *  controller.  This method takes a variable number of arguments.  All
	 *  arguments must be either objects conforming to the Model, Processing or
	 *  View interfaces or arrays filled entirely of those objects.
	 */
	public function connect(/* Var Args */)
	{
		$args = func_get_args();

		foreach ($args as $arg)
		{
			// Allow the connection of arrays of objects.
			if (is_array($arg))
			{
				call_user_func_array(array($this, 'connect'), $arg);
				continue;
			}
			
			if ($arg instanceof Iface\Model)
			{
				$this->connectModel($arg);
			}
			elseif ($arg instanceof Iface\Processing)
			{
				$this->connectProcessing($arg);
			}
			elseif ($arg instanceof Iface\View)
			{
				$this->connectView($arg);
			}
			else
			{
				throw new \DomainException(
					__METHOD__ . ' requires object with an interface of Model, ' .
					'Processing or View');
			}
		}
	}

	/** Execute the Controller.  Process user input, retrieve data from the
	 *  Model(s) and send it to the View(s).  Disconnect all objects from the
	 *  controller.
	 */   
	public function execute()
	{
		$this->doProcessing();
		$this->getData();
		$this->write();
		$this->resetData();
		$this->disconnectAll();
	}   
	
	/*********************/
	/* Protected Methods */
	/*********************/

	/// Disconnect all objects from the controller.
	protected function disconnectAll()
	{
		foreach ($this->connectedModels as $Model)
		{
			$this->EventManager->disconnect($this->events['Model'],
			                                array($Model, 'notifyData'));
		}

		foreach ($this->connectedProcessing as $Processing)
		{
			$this->EventManager->disconnect($this->events['Processing'],
			                                array($Processing, 'process'));
		}

		foreach ($this->connectedViews as $View)
		{
			$this->EventManager->disconnect($this->events['View'],
			                                array($View, 'write'));
 		}

		$this->connectedModels     = array();
		$this->connectedProcessing = array();
		$this->connectedViews      = array();
	}

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
	
	/*******************/
	/* Private Methods */
	/*******************/

	/** Connect a Model to the controller.
	 *  @param Model \object The model to connect.
	 */
	private function connectModel(Iface\Model $Model)
	{
		$this->EventManager->connect($this->events['Model'],
		                             array($Model, 'notifyData'));
		$this->connectedModels[] = $Model;
	}
	
	/** Connect a Processing object to the controller.
	 *  @param Processing \object The processing to connect.
	 */
	private function connectProcessing(Iface\Processing $Processing)
	{
		$this->EventManager->connect($this->events['Processing'],
		                             array($Processing, 'process'));
		$this->connectedProcessing[] = $Processing;
	}
	
	/** Connect a View to the controller.
	 *  @param View \object The view to connect.
	 */
	private function connectView(Iface\View $View)
	{
		$this->EventManager->connect($this->events['View'],
		                             array($View, 'write'));
		$this->connectedViews[] = $View;
	}
}
// EOF