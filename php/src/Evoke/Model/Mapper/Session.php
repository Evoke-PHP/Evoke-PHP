<?php
namespace Evoke\Model\Mapper;

use Evoke\Iface;

class Session implements Iface\Model\Mapper
{
	/** @property $sessionManager
	 *  Session Manager \object
	 */
	protected $sessionManager;

	/** Construct a Session model.
	 *  @param SessionManager \object The Session Manager for the part of the
	 *  session we are modelling.
	 */
	public function __construct(Iface\SessionManager $sessionManager)
	{
		$this->sessionManager = $sessionManager;
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Get the data from the session.
	 *  @params The offset in the data to fetch.
	 */
	public function fetch(Array $params=array())
	{
		$session = $this->sessionManager->getAccess();

		foreach ($params as $sessionOffset)
		{
			$session =& $session[$sessionOffset];
		}

		return is_array($session) ? $session : NULL;
	}
}
// EOF