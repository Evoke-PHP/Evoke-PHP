<?php
namespace Evoke\HTTP\MediaType\Rule;

/** A Media Type rule that matches exactly the media type from the accept
 *  header
 */
class Match extends Base
{
	/** @property $match
	 *  The match for the media type.
	 */
	protected $match;

	/** Construct the Exact rule.
	 *  @param match \array The match required from the media type.
	 */
	public function __construct(/* String */ $outputFormat,
	                            Array        $match)
	{
		parent::__construct($outputFormat, $match);

		$this->match = $match;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/** Check to see if the rule matches.
	 *  @param mediaType \array The media type we are checking against.
	 *  @return \bool Whether the rule matches.
	 */
	public function isMatch($mediaType)
	{
		return $mediaType === $this->match;
	}
}
// EOF