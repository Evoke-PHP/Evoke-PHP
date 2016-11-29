<?php
declare(strict_types = 1);
/**
 * HTTP Media Type Exact Rule
 *
 * @package Network\HTTP\MediaType\Rule
 */
namespace Evoke\Network\HTTP\MediaType\Rule;

/**
 * HTTP Media Type Exact Rule
 *
 * A Media Type rule that matches exactly the media type from the accept header.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Network\HTTP\MediaType\Rule
 */
class Exact extends Match
{
    /**
     * Fields that can be ignored in the match.
     *
     * @var string[]
     */
    protected $ignoredFields;

    /**
     * The match for the media type.
     *
     * @var mixed[]
     */
    protected $match;

    /**
     * Construct the Exact rule.
     *
     * @param string   $outputFormat  The output format for the rule.
     * @param mixed[]  $match         Exact match required from the media type.
     * @param string[] $ignoredFields Fields to be ignored in the match.
     */
    public function __construct(string $outputFormat, array $match, array $ignoredFields = ['q_factor'])
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
    public function isMatch() : bool
    {
        $mType = $this->mediaType;

        foreach ($this->ignoredFields as $ignored) {
            unset($mType[$ignored]);
        }

        return $mType === $this->match;
    }
}
// EOF
