<?php
namespace Evoke\Model\DB;
/// Provides the basic implementation for a database model.
abstract class Base extends \Evoke\Model\Base
{ 
	protected $sQL;

	public function __construct(Array $setup)
	{
		$setup += array('SQL' => NULL);

		if (!$sQL instanceof \Evoke\Core\DB\SQL)
		{
			throw new \InvalidArgumentException(__METHOD__ . ' requires SQL');
		}

		parent::__construct($setup);
 
		$this->sQL = $sQL;
	}
}
// EOF