<?php
/**
 * Metadata Interface
 *
 * @package Model\Data\Metadata
 */
namespace Evoke\Model\Data\Metadata;

/**
 * Metadata Interface
 *
 * Interface for metadata.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Model\Data\Metadata
 */
interface MetadataIface
{
	/**
	 * Arrange a set of results according to the Join tree.
	 *
	 * @param mixed[] The flat result data.
	 * @return mixed[] The data arranged into a hierarchy by the joins.
	 */
	public function arrangeFlatData(Array $results);

	/**
	 * Get the join ID for the specified join or throw an exception if it can't
	 * be found uniquely.
	 *
	 * @param string Join to get the ID for.
	 * @return string The full uniquely matched join ID.
	 * @throws DomainException If the join cannot be found uniquely.
	 */
	public function getJoinID($join);
	
}
// EOF