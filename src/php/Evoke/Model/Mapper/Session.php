<?php
namespace Evoke\Model\Mapper;

use Evoke\Model\Mapper\MapperIface,
	Evoke\Persistence\SessionManagerIface,
	RuntimeException;

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
	 * Create some data in the session.
	 *
	 * @param mixed[] The data to create.
	 */
	public function create(Array $data = array())
	{
		$this->sessionManager->setData($data);
	}

	/**
	 * Delete some data from storage.
	 *
	 * @param mixed[] The offset in the data to delete.
	 */
	public function delete(Array $params = array())
	{
		$this->sessionManager->deleteAtOffset($params);
	}

	/**
	 * Get the data from the session.
	 *
	 * @params The offset in the data to fetch.
	 */
	public function read(Array $params = array())
	{
		$session = $this->sessionManager->getAccess();

		foreach ($params as $sessionOffset)
		{
			if (!isset($session[$sessionOffset]))
			{
				$session = NULL;
				break;
			}
			
			$session =& $session[$sessionOffset];
		}

		return $session;
	}

	/**
	 * Update some data from the storage mechanism.
	 *
	 * @param mixed[] The old data from storage.
	 * @param mixed[] The new data to set it to.
	 */
	public function update(Array $old = array(),
	                       Array $new = array())
	{
		$session = $this->sessionManager->getAccess();
		
		// Ensure the the session has not been modified from the old values.
		if ($session != $old)
		{
			throw new RuntimeException(
				'Session has been modified before update.');
		}

		$this->sessionManager->setData($new);
	}
}
// EOF