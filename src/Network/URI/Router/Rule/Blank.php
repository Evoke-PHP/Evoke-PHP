<?php
declare(strict_types = 1);
/**
 * Blank Rule
 *
 * @package Evoke\Network\URI\Router\Rule
 */
namespace Evoke\Network\URI\Router\Rule;

/**
 * Blank Rule
 *
 * A rule to match blank URIs.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Evoke\Network\URI\Router\Rule
 */
class Blank extends Rule
{
    /**
     * Value for a blank URI.
     *
     * @var string
     */
    protected $value;

    /**
     * Construct the Blank URI Rule.
     *
     * @param string $value         Value for a blank URI.
     * @param bool   $authoritative Whether the rule can definitely give the final route for all URIs that it matches.
     */
    public function __construct(string $value, bool $authoritative = true)
    {
        parent::__construct($authoritative);

        $this->value = $value;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the controller.
     *
     * @return string The uri for the blank controller.
     */
    public function getController() : string
    {
        return $this->value;
    }

    /**
     * Check the uri to see if it matches.
     *
     * @return bool Whether the uri is matched.
     */
    public function isMatch() : bool
    {
        return empty($this->uri);
    }
}
// EOF
