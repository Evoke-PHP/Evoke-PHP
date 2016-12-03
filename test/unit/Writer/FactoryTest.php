<?php
namespace Evoke_Test\Writer;

use Evoke\Writer\Factory;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\Writer\Factory
 * @uses   Evoke\Writer\Writer
 */
class FactoryTest extends PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    /**
     * Create a HTML writer.
     *
     * @uses Evoke\Writer\HTML5
     */
    public function testCreateHTML5()
    {
        $object = new Factory;
        $this->assertInstanceOf('Evoke\Writer\HTML5', $object->create('HTML5'));
    }

    /**
     * Create a JSON writer.
     *
     * @uses Evoke\Writer\JSON
     */
    public function testCreateJSON()
    {
        $object = new Factory;
        $this->assertInstanceOf('Evoke\Writer\JSON', $object->create('JSON'));
    }

    /**
     * Create a Text writer.
     *
     * @uses Evoke\Writer\Text
     */
    public function testCreateText()
    {
        $object = new Factory;
        $this->assertInstanceOf('Evoke\Writer\Text', $object->create('Text'));
    }

    /**
     * Create an XHTML writer.
     *
     * @uses Evoke\Writer\XHTML
     */
    public function testCreateXHTML()
    {
        $object = new Factory;
        $this->assertInstanceOf('Evoke\Writer\XHTML', $object->create('XHTML'));
    }

    /**
     * Create an XML writer.
     *
     * @uses Evoke\Writer\XML
     */
    public function testCreateXML()
    {
        $object = new Factory;
        $this->assertInstanceOf('Evoke\Writer\XML', $object->create('XML'));
    }

    /**
     * Creating an unknown writer throws
     *
     * @expectedException DomainException
     */
    public function testCreateUnknown()
    {
        $object = new Factory;
        $object->create('UnkownOutputFormat');
    }
}
// EOF
