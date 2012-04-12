<?php
namespace Evoke\Core;

/** The Triad groups together Processing, Model and View objects that can work
 *  together to perform the functionality required by the Controllers.
 *
 *  The objects within the triad (and their function) are:
 *
 *  - Processing  -- Processing of inputs.
 *  - Model:      -- Retrieval of data.
 *  - View        -- Presentation of data.
 *
 *  Evoke differs from other MVC architectures by decoupling all of the layers
 *  within the MVC architecture using a very simple interface.  Having a simple
 *  and fixed interface for the objects that form a Triad allows for simple
 *  objects to be used so that the page can be built in parts with multiple
 *  Triads each forming components of the page.
 *
 *  The Triad uses an EventManager to decouple itself from the objects in
 *  the Model, Processing and View layers.  Any number of Model, Processing and
 *  View objects can be connected to the Triad.  On execution the Triad notifies
 *  the EventManager with the appropriate events to acheive the processing,
 *  retrieval and display of data.
 *
 *  After the Triad is executed any Model, Processing and View objects that were
 *  connected to it are disconnected, ready for it to be used with another group
 *  of objects.
 *
 *  Usage:
 *  \code
 *  $Triad = new \Evoke\Core\Triad($EventManager);
 *  $Triad->execute($Model, $Processing, $View,
 *                  $AnyAmountOfModelViewOrProcessingObjects); 
 *  \endcode
 */
class Triad
{
	/** @property $connectedModels
	 *  \array of Model objects connected to the Triad.
	 */
	private $connectedModels;

	/** @property $connectedProcessing
	 *  \array of Processing objects connected to the Triad.
	 */
	private $connectedProcessing;

	/** @property $connectedViews
	 *  \array of View objects connected to the Triad.
	 */
	private $connectedViews;

	/// Construct the Triad.
	public function __construct()
	{
		$this->connectedModels     = array();
		$this->connectedProcessing = array();
		$this->connectedViews      = array();		
	}

	/******************/
	/* Public Methods */
	/******************/
	
	/** Execute the Triad.
	 *  Process all user input, retrieve data from the Model(s) and send it to
	 *  the View(s).  Disconnect all objects from the Triad.
	 */   
	public function execute(/* Var Args */)
	{
		$this->connectObjects(func_get_args());

		if (empty($this->connectedProcessing))
		{
			throw new \BadMethodCallException(
				__METHOD__ . ' requires Processing object(s).');
		}
		elseif (empty($this->connectedModels))
		{
			throw new \BadMethodCallException(
				__METHOD__ . ' requires Model object(s).');
		}
		elseif (empty($this->connectedViews))
		{
			throw new \BadMethodCallException(
				__METHOD__ . ' requires View object(s).');
		}

		// Perform the processing.
		foreach ($this->connectedProcessing as $Processing)
		{
			$Processing->process();
		}

		$data = array();

		// Get the data from the models.
		foreach ($this->connectedModels as $Model)
		{
			$data = array_merge_recursive($data, $Model->getData());
		}

		// Write the data with the views.
		foreach ($this->connectedViews as $View)
		{
			$View->write($data);
		}

		// Disconnect the objects from the Triad.
		$this->connectedProcessing = array();
		$this->connectedModels     = array();
		$this->connectedViews      = array();
	}   
	
	/*********************/
	/* Protected Methods */
	/*********************/

	/** Connect Model, Processing and View objects (or arrays of them) to the
	 *  Triad.  This method takes a variable number of arguments.  All arguments
	 *  must be either objects conforming to the Model, Processing or View
	 *  interfaces or arrays filled entirely of those objects.
	 */
	protected function connectObjects(/* Var Args */)
	{
		$args = func_get_args();

		foreach ($args as $arg)
		{
			// Allow the connection of arrays of objects.
			if (is_array($arg))
			{
				// Using call_user_func_array the arguments of the array are
				// now the variable arguments for the recursive call.
				call_user_func_array(array($this, 'connectObjects'), $arg);
				continue;
			}
			
			if ($arg instanceof Iface\Model)
			{
				$this->connectedModels[] = $arg;
			}
			elseif ($arg instanceof Iface\Processing)
			{
				$this->connectedProcessing[] = $arg;
			}
			elseif ($arg instanceof Iface\View)
			{
				$this->connectedViews[] = $arg;
			}
			else
			{
				throw new \DomainException(
					__METHOD__ . ' requires object with an interface of Model, ' .
					'Processing or View');
			}
		}
	}
}
// EOF