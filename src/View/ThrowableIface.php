<?php
/**
 * ThrowableIface
 *
 * @package   Evoke\View
 */
namespace Evoke\View;

/**
 * ThrowableIface
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2016 Paul Young
 * @package   Evoke\View
 */
interface ThrowableIface extends ViewIface
{
    /**
     * Set the throwable that will be displayed by the view.
     *
     * @param \Throwable $throwable
     */
    public function set(\Throwable $throwable);
}
// EOF
