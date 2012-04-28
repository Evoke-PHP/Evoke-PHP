<?php
namespace Evoke\Model\Session;

use Evoke\Iface;

class Session extends \Evoke\Model\Base
{
	/** @property $sessionManager
	 *  Session Manager \object
	 */
	protected $sessionManager;

	/** Construct a Session model.
	 *  @param SessionManager \object The Session Manager for the part of the
	 *  session we are modelling.
	 *  @param dataPrefix \array Models return data at the specified prefix.
	 */
	public function __construct(Iface\SessionManager $sessionManager,
	                            Array                $dataPrefix = array())
	{
		parent::__construct($dataPrefix);
		
		$this->sessionManager = $sessionManager;
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