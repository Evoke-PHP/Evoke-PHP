<?php
namespace Evoke\Model\DB;

use Evoke\Iface\Core as ICore;

/// Provides the basic implementation for a database model.
abstract class Base extends \Evoke\Model\Base
{ 
	/** @property $sql
	 *  @object SQL
	 */
	protected $sql;

	/** Construct a Base object.
	 *  @param sql        @object SQL object.
	 *  @param dataPrefix @array  Data prefix to offset the data to.
	 */
	public function __construct(ICore\DB\SQL $sql,
	                            Array        $dataPrefix = array())
	{
		parent::__construct($dataPrefix);

		$this->sql = $sql;
	}
}
// EOF