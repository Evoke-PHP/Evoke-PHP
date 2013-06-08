<?php
namespace Evoke_Test\Writer;

use Evoke\Writer\XML,
	PHPUnit_Framework_TestCase;

/**
 * Test the base controller (and the abstract parts of it).
 *
 * @covers Evoke\Writer\XML
 * @covers Evoke\Writer\XMLBase
 */
class WriterTest extends PHPUnit_Framework_TestCase
{   
    /*********/
    /* Tests */
    /*********/

	public function test__construct()
	{
		$xIndex = 0;
		$xmlWriter = $this->getMock('XMLWriter');
		$xmlWriter
			->expects($this->at($xIndex++))
			->method('openMemory');
		$xmlWriter
			->expects($this->at($xIndex++))
			->method('setIndentString')
			->with('   ');
		$xmlWriter
			->expects($this->at($xIndex++))
			->method('setIndent')
			->with(true);
		
		$this->assertInstanceOf('Evoke\Writer\XML', new XML($xmlWriter));
	}

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

	public function testXMLWrite()
	{
		$expectedOutput = '<div class="Test"><span>Span_Text</span></div>';
		$object = new XML(new \XMLWriter, false);
		$object->write(['div',
		                ['class' => 'Test'],
		                [
			                ['span', [], 'Span_Text']]]);
		$this->assertEquals($expectedOutput, (string)($object));
	}
}
// EOF
