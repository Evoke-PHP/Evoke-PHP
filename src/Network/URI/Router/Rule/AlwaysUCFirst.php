<?php
declare(strict_types = 1);
/**
 * AlwaysUCFirst
 *
 * @package   Evoke\Network\URI\Router\Rule
 */
namespace Evoke\Network\URI\Router\Rule;

/**
 * AlwaysUCFirst
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\Network\URI\Router\Rule
 */
class AlwaysUCFirst extends Rule
{
    /**
     * @inheritdoc
     */
    public function __construct(bool $authoritative = false)
    {
        parent::__construct($authoritative);
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * @inheritDoc
     */
    public function getController() : string
    {
        return ucfirst($this->uri);
    }

    /**
     * @inheritDoc
     */
    public function isMatch() : bool
    {
        return true;
    }
}
// EOF
