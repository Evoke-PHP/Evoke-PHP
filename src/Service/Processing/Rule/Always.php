<?php
declare(strict_types = 1);
/**
 * Always
 *
 * @package   Evoke\Service\Processing\Rule
 */
namespace Evoke\Service\Processing\Rule;

/**
 * Always
 *
 * The always rule always matches so that processing definitely occurs.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\Service\Processing\Rule
 */
abstract class Always extends Rule
{
    /**
     * @inheritDoc
     */
    public function isMatch()
    {
        return true;
    }
}
// EOF
