<?php
/**
 * Data Builder
 *
 * @package Model\Data
 */
namespace Evoke\Model\Data;

use Evoke\Model\Data\Join\JoinIface;

/**
 * Data Builder
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Model\Data
 */
class DataBuilder
{
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Build hierarchical Data containers from the associated Join structure.
	 *
	 * @param JoinIface The join structure to build the data structure from.
	 */
    public function build(JoinIface $joinStructure)
	{
		$jointData = array();
		$joins = $joinStructure->getJoins();

		foreach ($joins as $joinID => $join)
		{
			$jointData[$joinID] = $this->build($join);
		}

		return new Data($joinStructure, $jointData);
	}
}
// EOF
