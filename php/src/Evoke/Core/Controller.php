<?php
namespace Evoke\Core;

class Controller
{
	protected $data;
	protected $em;
	protected $setup;

	public function __construct(Array $setup)
	{
		$this->setup = array_merge(array('EventManager' => NULL),
		                           $setup);

		if (!$this->setup['EventManager'] instanceof EventManager)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires EventManager');
		}

		$this->data = array();
		$this->em =& $this->setup['EventManager'];

		// Connect the Got Data for receiving data from models.
		$this->em->connect('Got_Data', array($this, 'addData'));
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Add the data (decoupled from the model and view by Event_Manager).
	 *  @param The data to be added for later notification to the view.
	 */
	public function addData($data)
	{
		$this->data = array_merge_recursive($this->data, $data);
	}

	/** Execute the controller action using the model to get and set information
	 *  and the view to display the information.  This is acheived by calling
	 *  generically named protected methods.  These protected methods can be
	 *  overriden in derived classes so that the execute method can be modified
	 *  part by part.
	 */   
	public function execute()
	{
		$this->processRequest();
		$this->getData();
		$this->write();
	}
   
	/*********************/
	/* Protected Methods */
	/*********************/

	/// Get the data from the model(s).
	protected function getData()
	{
		$this->em->notify('Model.Notify_Data');
	}
   
	/// Process the request (with all registered processors).
	protected function processRequest()
	{
		$this->em->notify('Request.Process');
	}

	/// Write the data with the view(s).
	protected function write()
	{
		$this->em->notify('View.Write', $this->data);
		$this->data = array();
	}
}
// EOF