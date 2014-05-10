<?php
/**
 * Join Interface
 *
 * @package Model\Data\Join
 */
namespace Evoke\Model\Data\Join;

/**
 * Join Interface
 *
 * Interface for metadata.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Model\Data\Join
 */
interface JoinIface
{
	/**
	 * Arrange a set of results according to the Join tree.
	 *
	 * @param mixed[] The flat result data.
	 * @return mixed[] The data arranged into a hierarchy by the joins.
	 */
	public function arrangeFlatData(Array $results);

	/**
	 * Get the canonical join ID for the specified join.
	 *
	 * @param string Join to get the ID for.
	 * @return string The canonical join ID.
	 * @throws DomainException If the join cannot be found uniquely.
	 */
	public function getJoinID($join);

	/**
	 * Get the joins from the join object. The join objects generally form tree
	 * structures, so these are the joins from the current node in the tree.
	 *
	 * @return JoinIface[] The joins from the object identified by their joinID.
	 */
	public function getJoins();		
}
// EOF