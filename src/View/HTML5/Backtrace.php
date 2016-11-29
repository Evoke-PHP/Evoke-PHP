<?php
declare(strict_types = 1);
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
 * @copyright Copyright (c) 2015 Paul Young
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
    public function get() : array
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
                        ['class' => 'file'],
                        empty($info['file']) ? '<internal>' : $info['file']
                    ]
                ];

            if (isset($info['line'])) {
                $infoElements[] = ['span', ['class' => 'line'], '(' . $info['line'] . ')'];
            }

            if (isset($info['class'])) {
                $infoElements[] = ['span', ['class' => 'class'], $info['class']];
            }

            if (isset($info['type'])) {
                $infoElements[] = ['span', ['class' => 'type'], $info['type']];
            }

            if (isset($info['function'])) {
                $infoElements[] = ['span', ['class' => 'function'], $info['function']];
            }

            $listItems[] = ['li', [], $infoElements];
        }

        return ['ol', ['class' => 'backtrace'], $listItems];
    }

    /**
     * Set the backtrace data.
     *
     * @param mixed[] $backtrace The backtrace data.
     */
    public function set(array $backtrace)
    {
        $this->backtrace = $backtrace;
    }
}
// EOF
