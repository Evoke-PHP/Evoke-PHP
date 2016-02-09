<?php
/**
 * Blank
 *
 * @package   Evoke\Network\URI\Processing\Rule
 */
namespace Evoke\Network\URI\Processing\Rule;

/**
 * Blank
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\Network\URI\Processing\Rule
 */
class Blank extends Rule
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        call_user_func($this->callable);
    }

    /**
     * @inheritDoc
     */
    public function isMatch()
    {
        return empty($this->data);
    }
}
// EOF
