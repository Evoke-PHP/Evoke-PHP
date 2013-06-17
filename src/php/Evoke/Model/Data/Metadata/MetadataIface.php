<?php
/**
 * MetadataIface
 *
 * @package Model
 */
namespace Evoke\Model\Data;

/**
 * MetadataIface
 *
 * Interface for metadata.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
interface MetadataIface
{
	/**
	 * Get the Joint data for the join field.
	 *
	 * @param string The field that joins the data.
	 *
	 * @returns DataIface The joint data.
	 */
	public function getJointData(/* String */ $joinField);
	
}
// EOF