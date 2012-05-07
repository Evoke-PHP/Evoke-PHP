<?php
namespace Evoke\Model\Mapper;

use Evoke\Iface;

/// Provides the basic implementation for a database based data mapper.
abstract class DB implements Iface\Model\Mapper
{ 
	/** @property $sql
	 *  @object SQL
	 */
	protected $sql;

	/** Construct a Base object.
	 *  @param sql        @object SQL object.
	 *  @param dataPrefix @array  Data prefix to offset the data to.
	 */
	public function __construct(Iface\DB\SQL $sql)
	{
		$this->sql = $sql;
	}
}
// EOF