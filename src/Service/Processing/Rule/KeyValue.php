<?php
/**
 * KeyValue
 *
 * @package   Evoke\Service\Processing\Rule
 */
namespace Evoke\Service\Processing\Rule;

/**
 * KeyValue
 *
 * {@inheritdoc} The callback is called with the value of the key.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\Service\Processing\Rule
 */
class KeyValue extends Key
{
    /******************/
    /* Public Methods */
    /******************/

    /**
     * @inheritDoc
     */
    public function execute()
    {
        call_user_func($this->callback, $this->data[$this->key]);
    }
}
// EOF
