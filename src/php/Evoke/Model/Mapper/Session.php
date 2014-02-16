<?php
/**
 * Session Mapper
 *
 * @package Model\Mapper
 */
namespace Evoke\Model\Mapper;

use Evoke\Model\Persistence\SessionIface,
	RuntimeException;

/**
 * Session Mapper
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Model\Mapper
 */
class Session
{
	/**
	 * Session
	 * @var Evoke\Model\Persistence\SessionIface
	 */
	protected $session;

	/**
	 * Construct a Session Mapper.
	 *
	 * @param SessionIface The session that we are mapping.
	 */
	public function __construct(SessionIface $session)
	{
		$this->session = $session;
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
		$this->session->setData($data);
	}

	/**
	 * Delete some data from storage.
	 *
	 * @param mixed[] The offset in the data to delete.
	 */
	public function delete(Array $params = array())
	{
		$this->session->deleteAtOffset($params);
	}

	/**
	 * Get the data from the session.
	 *
	 * @params The offset in the data to fetch.
	 */
	public function read(Array $params = array())
	{
		$session = $this->session->getAccess();

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
		$session = $this->session->getAccess();
		
		// Ensure the the session has not been modified from the old values.
		if ($session != $old)
		{
			throw new RuntimeException(
				'Session has been modified before update.');
		}

		$this->session->setData($new);
	}
}
// EOF