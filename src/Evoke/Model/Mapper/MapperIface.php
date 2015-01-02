<?php
/**
 * Mapper Interface
 *
 * @package Model\Mapper
 */
namespace Evoke\Model\Mapper;

/**
 * Mapper Interface
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Model\Mapper
 */
interface MapperIface
{
    /**
     * Create data in storage.
     *
     * @param mixed[] $data The data to create in storage as a simple array.
     */
    public function create(Array $data);

    /**
     * Delete data from storage.
     *
     * @param mixed[] $data The data to delete from storage.
     */
    public function delete(Array $data);

    /**
     * Read data from storage.
     *
     * @param mixed[] $match The data to match.
     */
    public function read(Array $match = []);

    /**
     * Update the matched data.
     *
     * @param mixed[] $match The data to match.
     * @param mixed[] $data  The data to set.
     */
    public function update(Array $match, Array $data);
}
// EOF
