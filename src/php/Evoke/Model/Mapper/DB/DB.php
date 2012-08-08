<?php
namespace Evoke\Model\Mapper\DB;

use Evoke\Model\Mapper\MapperIface,
	Evoke\Persistence\DB\SQLIface;

/**
 * DB
 *
 * The basic implementation for a database based data mapper.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
abstract class DB implements MapperIface
{
	/** 
	 * SQL Object
	 * @var Evoke\Persistence\DB\SQLIface
	 */
	protected $sql;

	/**
	 * Construct a DB Mapper.
	 *
	 *  @param Evoke\Persistence\DB\SQLIface SQL object.
	 */
	public function __construct(SQLIface $sql)
	{
		$this->sql = $sql;
	}
}
// EOF