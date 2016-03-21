<?php
/**
 * AlwaysUcfirst
 *
 * @package   Evoke\Network\URI\Router\Rule
 */
namespace Evoke\Network\URI\Router\Rule;

/**
 * AlwaysUcfirst
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\Network\URI\Router\Rule
 */
class AlwaysUcfirst extends Rule
{
    /**
     * AlwaysUcfirst constructor.
     */
    public function __construct($authoritative = false)
    {
        parent::__construct($authoritative);
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * @inheritDoc
     */
    public function getController()
    {
        return ucfirst($this->uri);
    }

    /**
     * @inheritDoc
     */
    public function isMatch()
    {
        return true;
    }
}
// EOF
