<?php
/**
 * Data Interface
 *
 * @package Model\Data
 */
namespace Evoke\Model\Data;

/**
 * Data Interface
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Model\Data
 */
interface FlatIface extends \ArrayAccess, \Iterator
{
    /**
     * Get the current record as a simple array (without iterator or class properties).
     *
     * @return mixed[] The current record as a simple array.
     */
    public function getRecord();

    /**
     * Whether the data is empty or not.
     *
     * @return bool Whether the data is empty or not.
     */
    public function isEmpty();

    /**
     * Set the data that we are managing.
     *
     * @param mixed[] $data The data we want to manage.
     */
    public function setData(Array $data);
}
// EOF
