<?php
declare(strict_types = 1);
/**
 * Blank
 *
 * @package   Evoke\Service\Processing\Rule
 */
namespace Evoke\Service\Processing\Rule;

/**
 * Blank
 *
 * The blank rule matches empty data and executes the processing using the callback without any parameters.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\Service\Processing\Rule
 */
class Blank extends Rule
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        call_user_func($this->callback);
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
