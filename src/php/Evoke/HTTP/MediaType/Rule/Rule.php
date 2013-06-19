<?php
/**
 * HTTP Media Type Rule Interface
 *
 * @package HTTP\MediaType\Rule
 */
namespace Evoke\HTTP\MediaType\Rule;

use InvalidArgumentException;

/**
 * HTTP Media Type Rule Interface
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   HTTP\MediaType\Rule
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