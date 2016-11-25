<?php
declare(strict_types = 1);
/**
 * AlwaysNone
 *
 * @package   Evoke\Service\Processing\Rule
 */
namespace Evoke\Service\Processing\Rule;

/**
 * AlwaysNone
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\Service\Processing\Rule
 */
class AlwaysNone extends Always
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        call_user_func($this->callback);
    }
}
// EOF
