<?php
declare(strict_types = 1);
/**
 * HTTP Media Type Rule Interface
 *
 * @package Network\HTTP\MediaType\Rule
 */
namespace Evoke\Network\HTTP\MediaType\Rule;

/**
 * HTTP Media Type Rule Interface
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Network\HTTP\MediaType\Rule
 */
abstract class Rule implements RuleIface
{
    /**
     * The media type that the rule is checked against.
     * @var mixed[]
     */
    protected $mediaType = [];

    /**
     * The output format.
     * @var string
     */
    protected $outputFormat;

    /**
     * Construct the Rule.
     *
     * @param string $outputFormat The output format for the rule.
     */
    public function __construct(string $outputFormat)
    {
        $this->outputFormat = $outputFormat;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the output format for the media type.
     *
     * @return string The output format.
     */
    public function getOutputFormat() : string
    {
        return $this->outputFormat;
    }

    /**
     * Set the media type that the rule is checked against.
     *
     * @param mixed[] The media type.
     */
    public function setMediaType(array $mediaType)
    {
        $this->mediaType = $mediaType;
    }
}
// EOF
