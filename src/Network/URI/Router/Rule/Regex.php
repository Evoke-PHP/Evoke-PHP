<?php
declare(strict_types = 1);
/**
 * URI Regex Rule
 *
 * @package Evoke\Network\URI\Router\Rule
 */
namespace Evoke\Network\URI\Router\Rule;

use InvalidArgumentException;

/**
 * URI Regex Rule
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Evoke\Network\URI\Router\Rule
 */
class Regex extends Rule
{
    /**
     * Controller regex match and replace.
     * @var string[]
     */
    protected $controller;

    /**
     * Regex to determine whether this rule matches.
     * @var string
     */
    protected $match;

    /**
     * Parameters each with a key and value regex for match and replace.
     * Example:
     * <pre><code>
     * [
     *     ['key'   => ['Match' => 'regex', 'Replace' => 'replacement'],
     *      'value' => ['Match' => 'regex', 'Replace' => 'replacement']]
     * ]
     * </code></pre>
     */
    protected $params;

    /**
     * Construct the Regex Rule.
     *
     * @param string[] $controller    Controller regex match and replace.
     * @param string   $match         Regex to determine whether the rule matches.
     * @param array[]  $params        Parameters each with a key and value regex for match and replacement.
     * @param bool     $authoritative Whether the rule is authoritative.
     * @throws InvalidArgumentException
     */
    public function __construct(Array $controller, $match, Array $params, $authoritative = false)
    {
        parent::__construct($authoritative);
        $invalidArgs = false;

        foreach ($params as $param) {
            if (!isset(
                $param['key']['match'],
                $param['key']['replace'],
                $param['value']['match'],
                $param['value']['replace']
            )) {
                $invalidArgs = true;
                break;
            }
        }

        if ($invalidArgs || !isset($controller['match'], $controller['replace'])) {
            throw new InvalidArgumentException('Bad Arguments');
        }

        $this->controller = $controller;
        $this->match      = $match;
        $this->params     = $params;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the controller.
     *
     * @return string The uri with the match replaced.
     */
    public function getController()
    {
        return preg_replace($this->controller['match'], $this->controller['replace'], $this->uri);
    }

    /**
     * Get any parameters.
     *
     * @return mixed[] Parameters from the URI.
     */
    public function getParams()
    {
        $paramsFound = [];

        foreach ($this->params as $param) {
            if (preg_match($param['key']['match'], $this->uri) &&
                preg_match($param['value']['match'], $this->uri)
            ) {
                $paramsFound[preg_replace($param['key']['match'], $param['key']['replace'], $this->uri)] =
                    preg_replace($param['value']['match'], $param['value']['replace'], $this->uri);
            }
        }

        return $paramsFound;
    }

    /**
     * Check the uri to see if it matches.
     *
     * @return bool Whether the uri is matched.
     */
    public function isMatch()
    {
        return preg_match($this->match, $this->uri) > 0;
    }
}
// EOF
