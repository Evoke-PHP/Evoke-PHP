<?php
/**
 * View Interface
 *
 * @package View
 */
namespace Evoke\View;

/**
 * View Interface
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   View
 */
interface ViewIface
{
    /**
     * Get the view ready for writing.
     *
     * @return mixed[] The output of the view.
     */
    public function get();
}
// EOF
