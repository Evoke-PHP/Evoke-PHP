<?php
namespace Evoke\Model\Mapper;

use InvalidArgumentException,
	RuntimeException;

class Backtrace implements MapperIface
{ 
	/** @property levelsToRetrace
	 *  @int LevelsToRetrace The number of levels to start the stack trace from.
	 */
	protected $levelsToRetrace;

	/** Construct a Backtrace object.
	 *  @param levelsToRetrace @int The number of levels up the trace to start.
	 */
	public function __construct(/* Int */ $levelsToRetrace = 2)
	{
		if (!is_int($levelsToRetrace))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires levelsToRetrace as int');
		}

		$this->levelsToRetrace = $levelsToRetrace;
	}

	
	/******************/
	/* Public Methods */
	/******************/

	public function fetch(Array $params = array())
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
