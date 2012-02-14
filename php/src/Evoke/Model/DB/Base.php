<?php
namespace Evoke\Model\DB;
/// Provides the basic implementation for a database model.
abstract class Base extends \Evoke\Model\Base
{ 
	protected $SQL;

	public function __construct(Array $setup)
	{
		$setup += array('SQL' => NULL);

		if (!$setup['SQL'] instanceof \Evoke\Core\DB\SQL)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires SQL');
		}

		parent::__construct($setup);
 
		$this->SQL = $setup['SQL'];
	}
}
// EOF