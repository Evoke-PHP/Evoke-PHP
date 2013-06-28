<?php
/**
 * HTTP Media Type Rule Interface
 *
 * @package Network\HTTP\MediaType\Rule
 */
namespace Evoke\Network\HTTP\MediaType\Rule;

/**
 * HTTP Media Type Rule Interface
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Network\HTTP\MediaType\Rule
 */
abstract class Rule implements RuleIface
{
	/**
	 * The output format.
	 * @var string
	 */
	protected $outputFormat;
	
	/**
	 * Construct the Rule.
	 *
	 * @param string The output format for the rule.
	 */
	public function __construct($outputFormat)
	{
		$this->outputFormat = $outputFormat;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the output format for the media type.
	 *
	 * @param mixed[] The media type.
	 */
	public function getOutputFormat(Array $mediaType)
	{
		return $this->outputFormat;
	}
}
// EOF