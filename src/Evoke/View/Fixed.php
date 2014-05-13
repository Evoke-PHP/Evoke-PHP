<?php
/**
 * Fixed View
 *
 * @package View
 */
namespace Evoke\View;

/**
 * Fixed View
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   View
 */
class Fixed implements ViewIface
{
    /**
     * Contents
     * @var mixed
     */
    protected $contents;

    /**
     * Construct a fixed view.
     *
     * @param mixed Contents.
     */
    public function __construct(/* mixed */ $contents)
    {
        $this->contents = $contents;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the fixed view.
     *
     * @return mixed[] The data for the view.
     */
    public function get()
    {
        return $this->contents;
    }
}
// EOF
