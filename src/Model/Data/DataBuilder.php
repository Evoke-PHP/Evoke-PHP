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
 * @copyright Copyright (c) 2015 Paul Young
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
     * @param JoinIface $joinStructure Structure to build the data from.
     * @return Data
     */
    public function build(JoinIface $joinStructure)
    {
        $jointData = [];
        $joins     = $joinStructure->getJoins();

        if (isset($joins)) {
            foreach ($joins as $joinID => $join) {
                $jointData[$joinID] = $this->build($join);
            }
        }

        return new Data($joinStructure, $jointData);
    }
}
// EOF
