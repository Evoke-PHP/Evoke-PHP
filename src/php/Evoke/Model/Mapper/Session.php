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
	 * Delete some data from the session.
	 *
	 * @param mixed[] The offset in the data to delete.
	 */
	public function delete(Array $offset = array())
	{
		$this->session->deleteAtOffset($offset);
	}

	/**
	 * Get the data from the session.
	 *
	 * @params mixed[]      The offset in the data to fetch.
	 * @return mixed[]|null Session data or null if the offset does not exist.
	 */
	public function read(Array $offset = array())
	{
		$session = $this->session->getCopy();

		foreach ($offset as $offsetPart)
		{
			if (!isset($session[$offsetPart]))
			{
				$session = NULL;
				break;
			}
			
			$session =& $session[$offsetPart];
		}

		return $session;
	}

	/**
	 * Update some data from the session.
	 *
	 * @param mixed[] The old data from session.
	 * @param mixed[] The new data to set it to.
	 */
	public function update(Array $old, Array $new)
	{
		$session = $this->session->getCopy();
		
		// Ensure the the session has not been modified from the old values.
		if ($session !== $old)
		{
			throw new RuntimeException(
				'Session update data has already been modified.');
		}

		$this->session->setData($new);
	}
}
// EOF