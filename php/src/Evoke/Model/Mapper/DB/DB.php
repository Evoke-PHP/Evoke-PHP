<?php
namespace Evoke\Model\Mapper\DB;

use Evoke\Model\MapperIface,
	Evoke\Persistance\DB\SQLIface;

/// Provides the basic implementation for a database based data mapper.
abstract class DB implements MapperIface
{ 
	/** @property $sql
	 *  @object SQL
	 */
	protected $sql;

	/** Construct a Base object.
	 *  @param sql        @object SQL object.
	 *  @param dataPrefix @array  Data prefix to offset the data to.
	 */
	public function __construct(SQLIface $sql)
	{
		$this->sql = $sql;
	}
}
// EOF