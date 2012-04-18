<?php
namespace Evoke\Iface\Core\DB\Table;

interface ListID
{
	/** Get a new List ID from the List_IDs table.
	 *  @param table \string The table name to get the List_ID for.
	 *  @param field \string The table field to get the List_ID for.
	 *  \return The new List_ID value or an exception is raised.
	 */
	public function getNew($table, $field);
}
// EOF