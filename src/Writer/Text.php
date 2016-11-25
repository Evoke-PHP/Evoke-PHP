<?php
declare(strict_types = 1);
/**
 * Text Writer
 *
 * @package Writer
 */
namespace Evoke\Writer;

/**
 * Text Writer
 *
 * Writer for Text (buffered).
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Writer
 */
class Text extends Writer
{
    /******************/
    /* Public Methods */
    /******************/

    /**
     * Write text into the buffer.
     *
     * @param string $text The text to write into the buffer.
     */
    public function write($text)
    {
        $this->buffer .= $text;
    }
}
// EOF
