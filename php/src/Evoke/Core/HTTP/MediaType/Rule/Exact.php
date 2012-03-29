<?php
namespace Evoke\Core\HTTP\MediaType\Rule;

/** A Media Type rule that matches exactly the media type from the accept
 *  header
 */
class Exact extends Base
{
	/** @property $ignoredFields
	 *  \array Fields that can be ignored in the match.
	 */
	protected $ignoredFields;

	/** Construct the Exact rule.
	 *  @param match \array The exact match required from the media type.
	 *  @param outputFormat \mixed The output format for the rule.
	 *  @param ignoredFields \array Any fields that are to be ignored in the
	 *  match.
	 */
	public function __construct($match,
	                            $outputFormat,
	                            Array $ignoredFields=array('Params', 'Q_Factor'))
	{
		parent::__construct($match, $outputFormat);

		$this->ignoredFields = $ignoredFields;
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
		// This cannot be type hinted as we can't override the method.
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