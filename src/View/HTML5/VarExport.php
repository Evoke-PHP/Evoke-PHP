<?php
declare(strict_types = 1);
/**
 * Variable Export View
 *
 * @package View\HTML5
 */
namespace Evoke\View\HTML5;

use Evoke\View\ViewIface;

/**
 * Variable Export View
 *
 * This view is useful for a quick view of data being passed to a view.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   View\HTML5
 */
class VarExport implements ViewIface
{
    /**
     * Variable to export.
     *
     * @var mixed
     */
    protected $var;

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the view of the parameters.
     *
     * @return mixed[] The view of the data.
     */
    public function get()
    {
        return ['div', ['class' => 'var_export'], var_export($this->var, true)];
    }

    /**
     * Set the var to export.
     *
     * @param mixed $var Var to export.
     */
    public function set($var)
    {
        $this->var = $var;
    }
}
// EOF
