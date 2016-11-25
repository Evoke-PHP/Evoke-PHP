<?php
/**
 * URI Upper Case First Rule
 *
 * @package Evoke\Network\URI\Router\Rule
 */
namespace Evoke\Network\URI\Router\Rule;

/**
 * URI Upper Case First Rule
 *
 * A rule to convert the first letter of each word to upper case. No parameters are matched by this class.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Evoke\Network\URI\Router\Rule
 */
class UpperCaseFirst extends Rule
{
    /**
     * The delimiters define the boundary of words.
     * @var string[]
     */
    protected $delimiters;

    /**
     * Construct the UpperCaseFirst Rule.
     *
     * @param string[] $delimiters    Delimiter strings that show the boundary of words.
     * @param bool     $authoritative Whether the rule can definitely give the final route for all URIs that it matches.
     */
    public function __construct(Array $delimiters, $authoritative = false)
    {
        parent::__construct($authoritative);

        $this->delimiters = $delimiters;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the controller with each word starting in upper case.
     *
     * @return string The string representing the Controller.
     */
    public function getController()
    {
        $controller = $this->uri;

        foreach ($this->delimiters as $delimiter) {
            $parts = explode($delimiter, $controller);

            foreach ($parts as &$part) {
                $part = ucfirst($part);
            }

            $controller = implode($delimiter, $parts);
        }

        return $controller;
    }

    /**
     * Check the uri to see if it matches.
     *
     * @return bool Whether the uri is matched.
     */
    public function isMatch()
    {
        foreach ($this->delimiters as $delimiter) {
            if (strpos($this->uri, $delimiter) !== false) {
                return true;
            }
        }

        return false;
    }
}
// EOF
