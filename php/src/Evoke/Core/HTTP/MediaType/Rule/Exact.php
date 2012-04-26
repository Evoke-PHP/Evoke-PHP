<?php
namespace Evoke\Core\HTTP\MediaType\Rule;

/** A Media Type rule that matches exactly the media type from the accept
 *  header
 */
class Exact extends Match
{
	/** @property $ignoredFields
	 *  @array Fields that can be ignored in the match.
	 */
	protected $ignoredFields;

	/** @property $match
	 *  The match for the media type.
	 */
	protected $match;

	/** Construct the Exact rule.
	 *  @param outputFormat  @string The output format for the rule.
	 *  @param match         @array Exact match required from the media type.
	 *  @param ignoredFields @array Fields that are to be ignored in the match.
	 */
	public function __construct(
		/* String */ $outputFormat,
		Array        $match,	                            
		Array        $ignoredFields = array('Params', 'Q_Factor'))
	{
		parent::__construct($outputFormat, $match);

		$this->ignoredFields = $ignoredFields;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/** Check to see if the rule matches.
	 *  @param mediaType @array The media type we are checking against.
	 *  @return @bool Whether the rule matches.
	 */
	public function isMatch($mediaType)
	{
		if (!is_array($mediaType))
		{
			throw new InvalidArgumentException(
				__METHOD__ . ' requires mediaType as array.');
		}

		foreach ($this->ignoredFields as $ignored)
		{
			unset($mediaType[$ignored]);
		}

		return $mediaType === $this->match;
	}
}
// EOF