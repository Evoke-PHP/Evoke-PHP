<?php
namespace Evoke\HTTP\MediaType\Rule;

use InvalidArgumentException;

/**
 * Exact
 *
 * A Media Type rule that matches exactly the media type from the accept header.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package HTTP
 */
class Exact extends Match
{
	/**
	 * Fields that can be ignored in the match.
	 * @var string[]
	 */
	protected $ignoredFields;

	/**
	 * The match for the media type.
	 * @var mixed[]
	 */
	protected $match;

	/**
	 * Construct the Exact rule.
	 * 
	 * @param string   The output format for the rule.
	 * @param mixed[]  Exact match required from the media type.
	 * @param string[] Fields that are to be ignored in the match.
	 */
	public function __construct(/* String */ $outputFormat,
	                            Array        $match,	                            
	                            Array        $ignoredFields = array('Q_Factor'))
	{
		parent::__construct($outputFormat, $match);

		$this->ignoredFields = $ignoredFields;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Check to see if the rule matches.
	 *
	 * @param mixed[] The media type we are checking against.
	 * @return bool Whether the rule matches.
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