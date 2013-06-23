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
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Model\Data\Metadata
 */
interface MetadataIface
{
	/**
	 * Arrange a set of results according to the Join tree.
	 *
	 * @param mixed[] The flat result data.
	 * @param mixed[] The data already processed from the results.
	 *
	 * @returns mixed[] The data arranged into a hierarchy by the joins.
	 */
	public function arrangeFlatData(Array $results, Array $data=array());

	/**
	 * Get the join ID for the specified join or throw an exception if it can't
	 * be found uniquely.
	 *
	 * @param string Join to get the ID for.
	 *
	 * @throws DomainException
	 */
	public function getJoinID($join);
	
}
// EOF