<?php
namespace Evoke_Test\Writer;

use Evoke\Writer\XML,
	PHPUnit_Framework_TestCase;

/**
 * Test the base controller (and the abstract parts of it).
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
	 * @covers       Evoke\Writer\XML::__construct
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
	 *
	 * @covers Evoke\Writer\XML::__toString
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
		$this->assertEquals('Whatever', (string)$object);
	}

	/**
	 * Cleanable.
	 *
	 * @covers Evoke\Writer\XML::clean
	 */
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
		$this->assertEquals('', (string)$object);
	}

	/**
	 * Flushes to the output.
	 *
	 * @covers Evoke\Writer\XML::__toString
	 * @covers Evoke\Writer\XML::flush
	 */
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
		
		$this->assertEquals('Flush Whatever', $flushed);
		$this->assertEquals('', (string)$object);

	}

	/**
	 * XML is Page Based.
	 *
	 * @covers Evoke\Writer\XML::isPageBased
	 */
	public function testIsPageBased()
	{
		$object = new XML($this->getMock('XMLWriter'));
		$this->assertTrue($object->isPageBased(), 'XML is page based.');
	}

	/**
	 * Start the document with default values.
	 *
	 * @covers Evoke\Writer\XML::writeStart
	 */
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

	/**
	 * Start an HTML5 document.
	 *
	 * @covers Evoke\Writer\XML::writeStart
	 */
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
	 * @covers Evoke\Writer\XML::writeStart
	 * @expectedException DomainException
	 */
	public function testWriteStartUnknown()
	{
		$object = new XML($this->getMock('XMLWriter'), 'UNKNOWN_DOCTYPE');
		$object->writeStart();
	}
	
	/**
	 * Start an XML document.
	 *
	 * @covers Evoke\Writer\XML::writeStart
	 */
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
	
	/**
	 * Write the end of a document.
	 *
	 * @covers Evoke\Writer\XML::writeEnd	 
	 */
	public function testWriteEnd()
	{
		$xmlWriter = $this->getMock('XMLWriter');
		$xmlWriter
			->expects($this->any())
			->method('endDocument')
			->with();
		
		$object = new XML($xmlWriter);
		$object->writeEnd();		
	}

	/**
	 * Write the start of a document.
	 *
	 * @covers Evoke\Writer\XML::writeStart
	 */
	public function testWriteStart()
	{
		$xmlWriter = $this->getMock('XMLWriter');
		$xmlWriter
			->expects($this->any())
			->method('startDocument')
			->with('1.0', 'UTF-8');
		
		$object = new XML($xmlWriter);
		$object->writeStart();
	}

	/**
	 * Write XML.
	 *
	 * @covers Evoke\Writer\XML::write
	 */
	public function testWriteXML()
	{
		$expectedOutput = '<div class="Test"><span>Span_Text</span></div>';
		$object = new XML(new \XMLWriter, 'XML', 'EN', false);
		$object->write(['div',
		                ['class' => 'Test'],
		                [
			                ['span', [], 'Span_Text']]]);
		$this->assertEquals($expectedOutput, (string)($object));
	}

	/**
	 * Writing a bad attrib throws.
	 *
	 * @covers            Evoke\Writer\XML::write
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
	 * @covers            Evoke\Writer\XML::write
	 * @expectedException InvalidArgumentException
	 */
	public function testWriteXMLBadTag()
	{
		$object = new XML($this->getMock('XMLWriter'));
		$object->write([NULL, 'a', 'b']);
	}

	/**
	 * Writing an inline element turns off indenting during the write.
	 *
	 * @covers Evoke\Writer\XML::write
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
