<?php
/**
 * HTTP Media Type Exact Rule
 *
 * @package Network\HTTP\MediaType\Rule
 */
namespace Evoke\Network\HTTP\MediaType\Rule;

use InvalidArgumentException;

/**
 * HTTP Media Type Exact Rule
 *
 * A Media Type rule that matches exactly the media type from the accept header.
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Network\HTTP\MediaType\Rule
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
	 * @return bool Whether the rule matches.
	 */
	public function isMatch()
	{
		$mType = $this->mediaType;
		
		foreach ($this->ignoredFields as $ignored)
		{
			unset($mType[$ignored]);
		}

		return $mType === $this->match;
	}
}
// EOF