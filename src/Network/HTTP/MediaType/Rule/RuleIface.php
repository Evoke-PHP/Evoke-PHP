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
interface RuleIface
{
    /**
     * Get the output format for the media type.
     *
     * @return string The output format.
     */
    public function getOutputFormat();

    /**
     * Check to see if the rule matches.
     *
     * @return bool Whether the rule matches.
     */
    public function isMatch();

    /**
     * Set the media type that the rule is checked against.
     *
     * @param mixed[] $mediaType
     */
    public function setMediaType(Array $mediaType);
}
// EOF
