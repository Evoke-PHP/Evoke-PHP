<?php
/**
 * Exception View
 *
 * @package View\HTML5
 */
namespace Evoke\View\HTML5;

use Evoke\View\ViewIface,
    LogicException;

/**
 * Exception View
 *
 * @author Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license MIT
 * @package View\HTML5
 */
class Exception implements ViewIface
{
    /**
     * The exception that we are viewing.
     * @var \Exception
     */
    protected $exception;

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the view (of the data) to be written.
     *
     * @return mixed[] The view data.
     */
    public function get()
    {
        if (!isset($this->exception))
        {
            throw new LogicException('needs exception to be set.');
        }

        return ['div',
                ['class' => 'Exception'],
                [['div', ['class' => 'Type'], get_class($this->exception)],
                 ['p', ['class' => 'Message'], $this->exception->getMessage()],
                 ['pre',
                  ['class' => 'Trace'],
                  $this->exception->getTraceAsString()]]];
    }

    /**
     * Set the exception for the view.
     *
     * @param \Exception $exception The exception for the view.
     */
    public function set(\Exception $exception)
    {
        $this->exception = $exception;
    }
}
// EOF
