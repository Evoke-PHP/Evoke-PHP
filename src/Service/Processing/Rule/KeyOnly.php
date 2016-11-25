<?php
declare(strict_types = 1);
/**
 * KeyOnly
 *
 * @package   Evoke\Service\Processing\Rule
 */

namespace Evoke\Service\Processing\Rule;

/**
 * KeyOnly
 *
 * {@inheritdoc} The callback is called without any parameters.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\Service\Processing\Rule
 */
class KeyOnly extends Key
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
