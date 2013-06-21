<?php
/**
 * Backtrace Mapper
 *
 * @package Model\Mapper
 */
namespace Evoke\Model\Mapper;

use RuntimeException;

/**
 * Backtrace Mapper
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model\Mapper
 */
class Backtrace implements ReadIface
{
	/** 
	 * The number of levels up the stack to start the backtrace from.
	 * @var int
	 */
	protected $levelsToRetrace;

	/**
	 * Construct a Backtrace object.
	 *
	 * @param int The number of levels up the stack to start the backtrace from.
	 */
	public function __construct(/* Int */ $levelsToRetrace = 2)
	{
		$this->levelsToRetrace = $levelsToRetrace;
	}
	
	/******************/
	/* Public Methods */
	/******************/
	
	/**
	 * Read the backtrace.
	 *
	 * @param mixed[] The conditions to match in the mapped data.
	 */
	public function read(Array $params = array())
	{
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

		if (count($trace) < $this->levelsToRetrace)
		{
			throw new RuntimeException(
				'Levels to retrace must always be less than the number of ' .
				'stack levels that we have.');
		}

		for ($i = $this->levelsToRetrace; $i > 0; $i--)
		{
			array_shift($trace);
		}

		$pascalCasedTrace = array();

		foreach ($trace as $entry)
		{
			$fixedEntry = array();
			
			foreach ($entry as $key => $value)
			{
				$fixedEntry[ucfirst($key)] = $value;
			}

			$pascalCasedTrace[] = $fixedEntry;
		}

		return $pascalCasedTrace;
	}
}
// EOF
