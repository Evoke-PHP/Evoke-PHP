<?php
/**
 * Throwable
 *
 * @package   Evoke\View\HTML5
 */
namespace Evoke\View\HTML5;

use Evoke\View\ThrowableIface;
use LogicException;

/**
 * Throwable
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @package   Evoke\View\HTML5
 */
class Throwable implements ThrowableIface
{
    /**
     * @var \Throwable
     */
    protected $throwable;

    /**
     * @inheritDoc
     */
    public function get()
    {
        if (!isset ($this->throwable)) {
            throw new LogicException('Throwable must be set to get view.');
        }

        return [
            'div',
            ['class' => 'Throwable'],
            [
                [
                    'div',
                    ['class' => 'location'],
                    'Thrown at ' . $this->throwable->getFile() . ' line ' . $this->throwable->getLine()
                ],
                ['pre', [], $this->throwable->getMessage()],
                ['pre', ['class' => 'trace'], $this->throwable->getTraceAsString()]
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function set(\Throwable $throwable)
    {
        $this->throwable = $throwable;
    }
}
// EOF
