<?php
namespace Evoke_Test\Writer;

use Evoke\Writer\Text,
    PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Writer\Text
 * @uses   Evoke\Writer\Writer
 */
class TextTest extends PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    /**
     * Create an object.
     */
    public function testCreate()
    {
        $object = new Text;
        $this->assertInstanceOf('Evoke\Writer\Text', $object);
    }

    /**
     * The writer can be cleaned.
     */
    public function testClean()
    {
        $object = new Text;
        $object->write('SOMETHING');
        $object->clean();
        $this->assertSame('', (string)$object);
    }

    /**
     * Flushing sends the output and cleans the buffer.
     */
    public function testFlush()
    {
        $object = new Text;
        $object->writeStart();
        $object->write('Something to Flush');

        ob_start();
        $object->flush();
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertSame('Something to Flush', $output);
        $this->assertSame('', (string)$object);
    }

    /**
     * Buffer starts empty even after initialization.
     */
    public function testStartsEmpty()
    {
        $object = new Text;
        $object->writeStart();
        $this->assertSame('', (string)$object);
    }

    /**
     * Text can be added to the writer and returned.
     */
    public function testTextWriting()
    {
        $object = new Text;
        $object->write('YO DUDE');
        $this->assertSame('YO DUDE', (string)$object);
    }
}
// EOF