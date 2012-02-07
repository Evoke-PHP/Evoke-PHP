<?php
namespace Evoke\Model\DB;
/// Provides the basic implementation for a database model.
abstract class Base extends \Evoke\Model\Base
{ 
	protected $sql;

	public function __construct(Array $setup)
	{
		$setup += array('SQL' => NULL);
		parent::__construct($setup);

		if (!$this->setup['SQL'] instanceof \Evoke\Core\DB\SQL)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires SQL');
		}
 
		$this->sql =& $this->setup['SQL'];
	}
}
// EOF