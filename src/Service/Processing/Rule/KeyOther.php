<?php
declare(strict_types = 1);
/**
 * KeyOther
 *
 * @package   Evoke\Service\Processing\Rule
 */

namespace Evoke\Service\Processing\Rule;

/**
 * KeyOther
 *
 * {@inheritdoc} The callback is called from the other data (i.e the data with the key removed).
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\Service\Processing\Rule
 */
class KeyOther extends Key
{
    /******************/
    /* Public Methods */
    /******************/

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $data = $this->data;
        unset($data[$this->key]);

        call_user_func($this->callback, $data);
    }
}
// EOF
