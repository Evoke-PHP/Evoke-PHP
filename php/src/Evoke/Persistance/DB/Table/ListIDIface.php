<?php
namespace Evoke\Persistance\DB\Table;

interface ListIDIface
{
	/** Get a new List ID from the List_IDs table.
	 *  @param table @string The table name to get the List_ID for.
	 *  @param field @string The table field to get the List_ID for.
	 *  @return The new List_ID value or an exception is raised.
	 */
	public function getNew($table, $field);
}
// EOF