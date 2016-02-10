<?php
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
     *     ['Key'   => ['Match' => 'regex', 'Replace' => 'replacement'],
     *      'Value' => ['Match' => 'regex', 'Replace' => 'replacement']]
     * ]
     * </code></pre>
     */
    protected $params;

    /**
     * Construct the Regex Rule.
     *
     * @param string[] $controller    Controller regex match and replace.
     * @param string   $match         Regex to determine whether the rule matches.
     * @param Array[]  $params        Parameters each with a key and value regex for match and replacement.
     * @param bool     $authoritative Whether the rule is authoritative.
     * @throws InvalidArgumentException
     */
    public function __construct(Array $controller, $match, Array $params, $authoritative = false)
    {
        parent::__construct($authoritative);
        $invalidArgs = false;

        foreach ($params as $param) {
            if (!isset(
                $param['Key']['Match'],
                $param['Key']['Replace'],
                $param['Value']['Match'],
                $param['Value']['Replace']
            )) {
                $invalidArgs = true;
                break;
            }
        }

        if ($invalidArgs || !isset($controller['Match'], $controller['Replace'])) {
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
        return preg_replace($this->controller['Match'], $this->controller['Replace'], $this->uri);
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
            if (preg_match($param['Key']['Match'], $this->uri) &&
                preg_match($param['Value']['Match'], $this->uri)
            ) {
                $paramsFound[preg_replace($param['Key']['Match'], $param['Key']['Replace'], $this->uri)] =
                    preg_replace($param['Value']['Match'], $param['Value']['Replace'], $this->uri);
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