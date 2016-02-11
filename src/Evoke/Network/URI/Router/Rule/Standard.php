<?php
/**
 * Standard
 *
 * @package   Evoke\Network\URI\Router\Rule
 */
namespace Evoke\Network\URI\Router\Rule;

/**
 * Standard
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\Network\URI\Router\Rule
 */
class Standard extends Rule
{
    /**
     * The controller to use when the rule matches.
     * @var string
     */
    protected $controller;

    /**
     * The URI path to match.
     * @var string
     */
    protected $path;

    /**
     * Standard constructor.
     *
     * @param string $controller
     * @param string $path
     * @param bool   $authoritative
     */
    public function __construct($controller, $path, $authoritative = true)
    {
        parent::__construct($authoritative);

        $this->controller = $controller;
        $this->path       = $path;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * @inheritDoc
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @inheritDoc
     */
    public function getParams()
    {
        $query = parse_url($this->uri, PHP_URL_QUERY);

        if (!$query) {
            return [];
        }

        parse_str($query, $params);

        return $params;
    }

    /**
     * @inheritDoc
     */
    public function isMatch()
    {
        return $this->path === parse_url($this->uri, PHP_URL_PATH);
    }
}
// EOF
