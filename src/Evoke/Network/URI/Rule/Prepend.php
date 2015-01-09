<?php
/**
 * URI Prepend Rule
 *
 * @package Network\URI\Rule
 */
namespace Evoke\Network\URI\Rule;

/**
 * URI Prepend Rule
 *
 * A rule to prepend a string to the controller.
 * No parameters are matched by this class.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Network\URI\Rule
 */
class Prepend extends Rule
{
    /**
     * String to prepend to the controller.
     *
     * @var string
     */
    protected $str;

    /**
     * Construct the prepend rule.
     *
     * @param string $str The string to prepend.
     * @param bool   $authoritative
     *                    Whether the rule can definitely give the final route
     *                    for all URIs that it matches.
     */
    public function __construct($str, $authoritative = false)
    {
        parent::__construct($authoritative);

        $this->str = $str;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the controller.
     *
     * @return string The uri with the string prepended.
     */
    public function getController()
    {
        return $this->str . $this->uri;
    }

    /**
     * The prepend rule always matches.
     *
     * @return bool TRUE.
     */
    public function isMatch()
    {
        return true;
    }
}
// EOF
