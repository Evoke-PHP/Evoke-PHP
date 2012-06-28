<?php
namespace Evoke\Model\Mapper\Session;

use Evoke\Model\Mapper\MapperIface,
	Evoke\Persistence\SessionManagerIface;

/**
 * Session Mapper
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
class Session implements MapperIface
{
	/**
	 * Session Manager
	 * @var Evoke\Persistence\SessionManagerIface
	 */
	protected $sessionManager;

	/**
	 * Construct a Session Mapper.
	 *
	 * @param Evoke\Persistence\SessionManagerIfaceSessionManager
	 *        The Session Manager for the part of the session we are mapping.
	 */
	public function __construct(SessionManagerIface $sessionManager)
	{
		$this->sessionManager = $sessionManager;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the data from the session.
	 *
	 * @params The offset in the data to fetch.
	 */
	public function fetch(Array $params = array())
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