<?php
namespace Evoke_Test\Writer;

use Evoke\Writer\XML,
    PHPUnit_Framework_TestCase;

/**
 * @covers       Evoke\Writer\XML
 */
class XMLTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerConstruct()
    {
        return ['Indent_On'       => [TRUE],
                'Indent_Off'      => [FALSE],
                'Indent_Specific' => [TRUE, '        ']];
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * Create the object.
     *
     * @dataProvider providerConstruct
     */
    public function test__construct($indent, $indentString = '   ')
    {
        $xIndex = 0;
        $xmlWriter = $this->getMock('XMLWriter');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('openMemory');

        if ($indent)
        {
            $xmlWriter
                ->expects($this->at($xIndex++))
                ->method('setIndentString')
                ->with($indentString);
            $xmlWriter
                ->expects($this->at($xIndex++))
                ->method('setIndent')
                ->with(true);
        }

        $object = new XML(
            $xmlWriter, 'XHTML_1_1', 'EN', $indent, $indentString);
        $this->assertInstanceOf('Evoke\Writer\XML', $object);
    }

    /**
     * Converts to a string.
     */
    public function testConvertsToAString()
    {
        $xIndex = 0;
        $xmlWriter = $this->getMock('XMLWriter');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('openMemory');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('outputMemory')
            ->with(FALSE)
            ->will($this->returnValue('Whatever'));

        $object = new XML($xmlWriter, 'XHTML_1_1', 'EN', FALSE);
        $this->assertSame('Whatever', (string)$object);
    }

    public function testCleanable()
    {
        $xIndex = 0;
        $xmlWriter = $this->getMock('XMLWriter');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('openMemory');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('outputMemory')
            ->with(TRUE);
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('outputMemory')
            ->with(FALSE)
            ->will($this->returnValue(''));

        $object = new XML($xmlWriter, 'XML', 'ES', FALSE);
        $object->clean();
        $this->assertSame('', (string)$object);
    }

    public function testFlush()
    {
        $xIndex = 0;
        $xmlWriter = $this->getMock('XMLWriter');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('openMemory');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('outputMemory')
            ->with(TRUE)
            ->will($this->returnValue('Flush Whatever'));
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('outputMemory')
            ->with(FALSE)
            ->will($this->returnValue(''));

        $object = new XML($xmlWriter, 'HTML5', 'EN', FALSE);
        ob_start();
        $object->flush();
        $flushed = ob_get_contents();
        ob_end_clean();

        $this->assertSame('Flush Whatever', $flushed);
        $this->assertSame('', (string)$object);

    }

    public function testWriteEnd()
    {
        $xIndex = 0;
        $xmlWriter = $this->getMock('XMLWriter');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('openMemory');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('endDocument')
            ->with();

        $object = new XML($xmlWriter, 'XML', 'EN', false);
        $object->writeEnd();
    }

    public function testWriteStartDefault()
    {
        $xIndex = 0;
        $xmlWriter = $this->getMock('XMLWriter');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('openMemory');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('setIndentString');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('setIndent');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('startDTD')
            ->with('html',
                   '-//W3C//DTD XHTML 1.1//EN',
                   'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('endDTD');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('startElementNS')
            ->with(null, 'html', 'http://www.w3.org/1999/xhtml');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('writeAttribute')
            ->with('lang', 'EN');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('writeAttribute')
            ->with('xml:lang', 'EN');

        $object = new XML($xmlWriter);
        $object->writeStart();
    }

    public function testWriteStartHTML5()
    {
        $xIndex = 3;
        $xmlWriter = $this->getMock('XMLWriter');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('startDTD')
            ->with('html');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('endDTD');

        $object = new XML($xmlWriter, 'HTML5');
        $object->writeStart();
    }

    /**
     * Writing the start of an unknown type of document throws.
     *
     * @expectedException DomainException
     */
    public function testWriteStartUnknown()
    {
        $object = new XML($this->getMock('XMLWriter'), 'UNKNOWN_DOCTYPE');
        $object->writeStart();
    }

    public function testWriteStartXML()
    {
        $xIndex = 3;
        $xmlWriter = $this->getMock('XMLWriter');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('startDocument')
            ->with('1.0', 'UTF-8');

        $object = new XML($xmlWriter, 'XML');
        $object->writeStart();
    }

    public function testWriteXML()
    {
        $expectedOutput = '<div class="Test"><span>Span_Text</span></div>';
        $object = new XML(new \XMLWriter, 'XML', 'EN', false);
        $object->write(['div',
                        ['class' => 'Test'],
                        [
                            ['span', [], 'Span_Text']]]);
        $this->assertSame($expectedOutput, (string)($object));
    }

    /**
     * Writing a bad attrib throws.
     *
     * @expectedException InvalidArgumentException
     */
    public function testWriteXMLBadAttribs()
    {
        $object = new XML($this->getMock('XMLWriter'));
        $object->write(['div', 'BadAttribs', 'b']);
    }

    /**
     * Writing a bad tag throws.
     *
     * @expectedException InvalidArgumentException
     */
    public function testWriteXMLBadTag()
    {
        $object = new XML($this->getMock('XMLWriter'));
        $object->write([NULL, 'a', 'b']);
    }

    /**
     * Writing an inline element turns off indenting during the write.
     */
    public function testWriteXMLInlineIndent()
    {
        $xIndex = 3;
        $xmlWriter = $this->getMock('XMLWriter');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('setIndent')
            ->with(FALSE);
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('startElement')
            ->with('pre');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('writeAttribute')
            ->with('A', 'Val');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('text')
            ->with("This text\nis inline\nOK\n");
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('endElement');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('setIndent')
            ->with(TRUE);

        $object = new XML($xmlWriter);
        $object->write(['pre', ['A' => 'Val'], "This text\nis inline\nOK\n"]);
    }
}
// EOF
