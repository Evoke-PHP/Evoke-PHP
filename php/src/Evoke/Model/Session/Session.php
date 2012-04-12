<?php
namespace Evoke\Model\Session;

use Evoke\Core\Iface;

class Session extends \Evoke\Model\Base
{
	/** @property $SessionManager
	 *  Session Manager \object
	 */
	protected $SessionManager;

	public function __construct(Array $setup)
	{
		$setup += array('Session_Manager' => NULL);
      
		parent::__construct($setup);

		if (!$setup['Session_Manager'] instanceof \Evoke\Core\SessionManager)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires SessionManager');
		}
	}

	/******************/
	/* Public Methods */
	/******************/

	// Get the data from the session.
	public function getData()
	{
		$session = $this->sessionManager->getAccess();

		if (!is_array($session))
		{
			return $this->offsetData(parent::getData());
		}
      
		return $this->offsetData(array_merge(parent::getData(), $session));
	}
}
// EOF