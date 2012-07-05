<?php
namespace Evoke\View\XHTML\Control\RecordList;

/**
 * Table
 *
 * View to display a list of records from a table.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Table extends RecordList
{ 
	/// @todo Fix to new View interface.
	public function __construct(Array $setup)
	{
		/// @todo Fix to new View interface.
		throw new \RuntimeException('Fix to new view interface.');

		$setup += array('Data'       => NULL,
		                'Table_Info' => NULL);

		if (!$this->tableInfo instanceof \Evoke\DB\Table\Info)
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Table_Info');
		}

		// Set specific fields in the setup for a table.
		$fields      = $tableInfo->getFields();
		$primaryKeys = $tableInfo->getPrimaryKeys();
		$tableName   = $tableInfo->getTableName();

		if (!isset($attribs))
		{
			$attribs =
				array('class' => 'Record_List ' . $tableName);
		}
  
		parent::__construct($setup);
	}
}
// EOF