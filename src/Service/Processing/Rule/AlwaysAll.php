<?php
declare(strict_types = 1);
/**
 * AlwaysAll
 *
 * @package   Evoke\Service\Processing\Rule
 */
namespace Evoke\Service\Processing\Rule;

/**
 * AlwaysAll
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\Service\Processing\Rule
 */
class AlwaysAll extends Always
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        call_user_func($this->callback, $this->data);
    }
}
// EOF
