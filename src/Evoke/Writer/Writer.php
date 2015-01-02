<?php
/**
 * Abstract Writer
 *
 * @package Writer
 */
namespace Evoke\Writer;

/**
 * Abstract Writer
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Writer
 */
abstract class Writer implements WriterIface
{
    /**
     * The buffer that holds the text that is to be written.
     *
     * @var string
     */
    protected $buffer;

    /**
     * Construct the buffered Writer.
     */
    public function __construct()
    {
        $this->buffer = '';
    }

    /**
     * Get the string representation of the buffer that we are writing to.
     */
    public function __toString()
    {
        return $this->buffer;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Reset the buffer that we are writing to.
     */
    public function clean()
    {
        $this->buffer = '';
    }

    /**
     * Flush the output buffer (send it and then reset).
     */
    public function flush()
    {
        echo $this->buffer;
        $this->buffer = '';
    }

    /**
     * By default nothing is required to end a document.
     */
    public function writeEnd()
    {

    }

    /**
     * By default nothing is required to start a document.
     */
    public function writeStart()
    {

    }
}
// EOF
