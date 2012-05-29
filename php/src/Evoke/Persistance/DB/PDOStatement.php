<?php
namespace Evoke\Persistance\DB;

use Evoke\Message\Exception\DB as ExceptionDB,
	Exception;

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
		catch (Exception $e)
		{
			throw new ExceptionDB(__METHOD__, 'Exception Raised: ', $this, $e);
		}
	 
		if ($result === false)
		{
			throw new ExceptionDB(__METHOD__, 'Execute False: ', $this);
		}
		else
		{
			return $result;
		}
	}
}
// EOF