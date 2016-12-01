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

        ob_start();
        var_dump($this->throwable->getTrace());
        $fullTrace = ob_get_clean();

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
                ['h2', [], 'Basic Trace'],
                ['pre', ['class' => 'basic_trace'], $this->throwable->getTraceAsString()],
                ['h2', [], 'Full Trace'],
                ['pre', ['class' => 'full_trace'], $fullTrace]
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
