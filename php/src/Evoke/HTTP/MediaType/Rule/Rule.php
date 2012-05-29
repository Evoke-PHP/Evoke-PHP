<?php
namespace Evoke\HTTP\MediaType;

use InvalidArgumentException;

abstract class Rule implements RuleIface
{
	/** @property $outputFormat
	 *  @mixed The output format.
	 */
	protected $outputFormat;
	
	/** Construct the Equivalent Rule.
	 *  @param match @array The match for the rule.
	 *  @param outputFormat @mixed The output format for the rule.
	 */
	public function __construct($outputFormat)
	{
		if (!is_string($outputFormat))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires outputFormat as string');
		}

		$this->outputFormat = $outputFormat;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/** Get the output format for the media type.
	 *  @param mediaType @array The media type.
	 */
	public function getOutputFormat(Array $mediaType)
	{
		return $this->outputFormat;
	}
}
// EOF