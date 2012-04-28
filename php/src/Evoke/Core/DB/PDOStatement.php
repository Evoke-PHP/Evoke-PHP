<?php
namespace Evoke\DB;

class PDOStatement extends \PDOStatement
{
	private $namedPlaceholders;
   
	protected function __construct($namedPlaceholders)
	{
		$this->namedPlaceholders = $namedPlaceholders;
	}

	/******************/
	/* Public Methods */
	/******************/

	public function execute($inputParameters=array())
	{
		try
		{
			if ($this->namedPlaceholders)
			{
				$result = parent::execute($inputParameters);
			}
			else
			{
				$result = parent::execute(array_values($inputParameters));
			}
		}
		catch (\Exception $e)
		{
			throw new \Evoke\Exception\DB(
				__METHOD__, 'Exception Raised: ', $this, $e);
		}
	 
		if ($result === false)
		{
			throw new \Evoke\Exception\DB(__METHOD__, 'Execute False: ', $this);
		}
		else
		{
			return $result;
		}
	}
}
// EOF