<?php
/**
 * Table Mapper for CRUD.
 *
 * @package Model
 */
namespace Evoke\Model\Mapper\DB;

use Evoke\Model\AdminIface;

/**
 * Table Mapper for CRUD.
 *
 * Provide a full mapper for a single database table.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
class Table extends TableRead implements MapperIface
{
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Create a record in the table.
	 *
	 * @param mixed[] The record to add.
	 */
	public function create(Array $data = array())
	{
		$this->sql->insert($this->tableName, array_keys($data), $data);
	}
	
	/**
	 * Delete record(s) from the table.
	 *
	 * @param mixed[] The record(s) to delete.
	 */
	public function delete(Array $params = array())
	{
		$this->sql->delete($this->tableName, $params);
	}
			
	/**
	 * Update a record in the table.
	 *  
	 * @param mixed[] The record to modify.
	 * @param mixed[] The value to set the record to.
	 */
	public function update(Array $old = array(), Array $new = array())
	{
		$this->sql->update($this->tableName, $new, $old);
	}
}
// EOF