<?php
namespace Evoke\DB;

interface SQLIface extends \Evoke\DBIface
{
	
	public function getAssoc($queryString, $params=array());
	public function getSingleRow($queryString, $params=array());
	public function getSingleValue($queryString, $params=array(), $column=0);
	public function select($tables, $fields, $conditions='', $order='', $limit=0, $distinct=false);
	public function selectSingleValue($table, $field, $conditions);
	public function update($tables, $setValues, $conditions='', $limit=0);
	public function delete($tables, $conditions);
	public function insert($table, $fields, $valArr);
	public function addColumn($table, $column, $fieldType);
	public function dropColumn($table, $column);
	public function changeColumn($table, $oldCol, $newCol, $fieldType);
}
