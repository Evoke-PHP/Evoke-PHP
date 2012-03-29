<?php
namespace Evoke\Core\HTTP\MediaType\Rule;

abstract class Base implements \Evoke\Core\Iface\HTTP\MediaType\Rule
{
	/** @property $match
	 *  The match for the media type.
	 */
	protected $match;
	
	/** @property $outputFormat
	 *  \mixed The output format.
	 */
	protected $outputFormat;
	
	/** Construct the Equivalent Rule.
	 *  @param match \array The match for the rule.
	 *  @param outputFormat \mixed The output format for the rule.
	 */
	public function __construct(Array $match, $outputFormat)
	{
		$this->match        = $match;
		$this->outputFormat = $outputFormat;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/** Get the output format for the media type.
	 *  @param mediaType \array The media type.
	 */
	public function getOutputFormat(Array $mediaType)
	{
		return $this->outputFormat;
	}
}
// EOF