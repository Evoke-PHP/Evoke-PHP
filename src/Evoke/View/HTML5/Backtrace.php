<?php
/**
 * Backtrace View
 *
 * @package View\HTML5
 */
namespace Evoke\View\HTML5;

use Evoke\View\ViewIface;
use LogicException;

/**
 * Backtrace View
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   View\HTML5
 */
class Backtrace implements ViewIface
{
    /**
     * Backtrace data.
     *
     * @var mixed[]
     */
    protected $backtrace;

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the view of the backtrace.
     *
     * @return mixed[] The view.
     * @throws LogicException If no backtrace has been set.
     */
    public function get()
    {
        if (empty($this->backtrace)) {
            throw new LogicException('needs backtrace.');
        }

        $listItems = [];

        foreach ($this->backtrace as $info) {
            $infoElements =
                [
                    [
                        'span',
                        ['class' => 'File'],
                        empty($info['file']) ? '<internal>' : $info['file']
                    ]
                ];

            if (isset($info['line'])) {
                $infoElements[] =
                    ['span', ['class' => 'Line'], '(' . $info['line'] . ')'];
            }

            if (isset($info['class'])) {
                $infoElements[] =
                    ['span', ['class' => 'Class'], $info['class']];
            }

            if (isset($info['type'])) {
                $infoElements[] =
                    ['span', ['class' => 'Type'], $info['type']];
            }

            if (isset($info['function'])) {
                $infoElements[] =
                    ['span', ['class' => 'Function'], $info['function']];
            }

            $listItems[] = ['li', [], $infoElements];
        }

        return ['ol', ['class' => 'Backtrace'], $listItems];
    }

    /**
     * Set the backtrace data.
     *
     * @param mixed[] $backtrace The backtrace data.
     */
    public function set(Array $backtrace)
    {
        $this->backtrace = $backtrace;
    }
}
// EOF
