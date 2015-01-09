<?php
/**
 * Error View
 *
 * @package View\HTML5
 */
namespace Evoke\View\HTML5;

use Evoke\View\ViewIface;
use LogicException;

/**
 * Error View
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   View\HTML5
 */
class Error implements ViewIface
{
    /**
     * Error to get the view of.
     *
     * @var string[]
     */
    protected $error;

    /**
     * String to use for an unknown part of the error.
     *
     * @var string
     */
    protected $unknown;

    /**
     * Construct an Error view.
     *
     * @param string $unknown String to use for an unknown part of the error.
     */
    public function __construct($unknown = '<Unknown>')
    {
        $this->unknown = $unknown;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the view (of the data) to be written.
     *
     * @return mixed[] The view data.
     * @throws LogicException
     */
    public function get()
    {
        if (!isset($this->error)) {
            throw new LogicException('needs error');
        }

        $error = array_merge(
            ['file'    => $this->unknown,
             'line'    => $this->unknown,
             'message' => $this->unknown,
             'type'    => $this->unknown],
            $this->error
        );

        return [
            'div',
            ['class' => 'Error'],
            [
                [
                    'div',
                    ['class' => 'Details'],
                    [
                        [
                            'span',
                            ['class' => 'Type'],
                            $this->getTypeString($error['type'])
                        ],
                        ['span', ['class' => 'File'], $error['file']],
                        ['span', ['class' => 'Line'], $error['line']]
                    ]
                ],
                ['p', ['class' => 'Message'], $error['message']]
            ]
        ];
    }

    /**
     * Set the error for the view.
     *
     * @param mixed[] Error .
     */
    public function set(Array $error)
    {
        $this->error = $error;
    }

    /*******************/
    /* Private Methods */
    /*******************/

    /**
     * Get the type of error message based on the error number.
     *
     * @param int $number The error number.
     * @return string The error type as a string.
     */
    private function getTypeString($number)
    {
        $errorMap = [
            E_ERROR             => 'E_ERROR',
            E_WARNING           => 'E_WARNING',
            E_PARSE             => 'E_PARSE',
            E_NOTICE            => 'E_NOTICE',
            E_CORE_ERROR        => 'E_CORE_ERROR',
            E_CORE_WARNING      => 'E_CORE_WARNING',
            E_CORE_ERROR        => 'E_COMPILE_ERROR',
            E_CORE_WARNING      => 'E_COMPILE_WARNING',
            E_USER_ERROR        => 'E_USER_ERROR',
            E_USER_WARNING      => 'E_USER_WARNING',
            E_USER_NOTICE       => 'E_USER_NOTICE',
            E_STRICT            => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED        => 'E_DEPRECATED',
            E_USER_DEPRECATED   => 'E_USER_DEPRECATED'
        ];

        if (isset($errorMap[$number])) {
            return $errorMap[$number];
        }

        return $this->unknown;
    }
}
// EOF
