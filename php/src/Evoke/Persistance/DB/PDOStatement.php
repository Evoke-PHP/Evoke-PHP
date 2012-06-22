<?php
namespace Evoke\Persistance\DB;

use Evoke\Message\Exception\DB as ExceptionDB,
	Exception;

/**
 * PDOStatement
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Persistance
 */
class PDOStatement extends \PDOStatement
{
	/**
	 * Whether we are using named placeholders.
	 * @var bool
	 */
	private $namedPlaceholders;

	/**
	 * Construct the PDOStatement object.
	 *
	 * @param bool Whether we are using named placeholders.
	 */
	protected function __construct($namedPlaceholders)
	{
		$this->namedPlaceholders = $namedPlaceholders;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Execute the statement.
	 *
	 * @param mixed[] The input parameters to the statement.
	 *
	 * @return mixed The result.
	 *
	 * @throw Evoke\Message\Exception\DB If the statement fails.
	 */
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