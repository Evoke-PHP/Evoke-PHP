<?php
namespace Evoke\Model\DB;

use Evoke\Iface;

/// Provides the basic implementation for a database model.
abstract class Base extends \Evoke\Model\Base
{ 
	/** @property $sql
	 *  SQL \object
	 */
	protected $sql;

	/** Construct a Base object.
	 *  @param sql        \object SQL object.
	 *  @param dataPrefix \array  Data prefix to offset the data to.
	 */
	public function __construct(Iface\Core\DB\SQL $sql,
	                            Array             $dataPrefix=array())
	{
		parent::__construct($dataPrefix);

		$this->sql = $sql;
	}
}
// EOF