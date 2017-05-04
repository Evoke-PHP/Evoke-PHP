<?php
declare(strict_types = 1);
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
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Model\Data
 */
interface FlatIface extends \ArrayAccess, \Countable, \Iterator
{
    /**
     * Get the current record as a simple array (without iterator or class properties).
     *
     * @return mixed[] The current record as a simple array.
     */
    public function getRecord() : array;

    /**
     * Whether the data is empty or not.
     *
     * @return bool Whether the data is empty or not.
     */
    public function isEmpty() : bool;

    /**
     * Reset the data that we are managing to empty.
     */
    public function reset();

    /**
     * Set the data that we are managing.
     *
     * @param mixed[] $data The data we want to manage.
     */
    public function setData(array $data);
}
// EOF
