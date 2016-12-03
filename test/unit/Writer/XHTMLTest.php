<?php
namespace Evoke_Test\Writer;

use Evoke\Writer\XHTML;
use LogicException;
use PHPUnit_Framework_TestCase;

/**
 * @covers       Evoke\Writer\XHTML
 */
class XHTMLTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerWriteBadChild()
    {
        return [
            'Child_Arr_Too_Long'  => ['xhtml' => ['a', [], [[1, 2, 3, 4]]]],
            'Child_Arr_Too_Short' => ['xhtml' => ['a', [], [[1, 2]]]],
            'Child_Non_Arr_Str'   => ['xhtml' => ['a', ['b' => '2'], [2]]],
            'Child_Second_Bad'    => [
                'xhtml' =>
                    [
                        'a',
                        [],
                        [
                            ['div', [], 'Good'],
                            ['span', 'BAD']
                        ]
                    ]
            ]
        ];
    }

    public function providerWriteBadRoot()
    {
        return [
            'Array_Too_Few'       => ['xhtml' => ['a', ['b' => '2']]],
            'Array_Too_Many'      => ['xhtml' => ['1', '2', '3', '4']],
            'Non_Array_Or_String' => ['xhtml' => 213]

        ];
    }

    /*********/
    /* Tests */
    /*********/

    public function testWrite()
    {
        $expectedOutput = '<div class="Test"><span>Span_Text</span></div>';
        $object         = new XHTML(new \XMLWriter, false);
        $object->write([
            'div',
            ['class' => 'Test'],
            [
                ['span', [], 'Span_Text']
            ]
        ]);
        $this->assertSame($expectedOutput, (string)($object));
    }

    /**
     * Writing a bad attrib throws.
     *
     * @expectedException LogicException
     * @expectedExceptionMessage Failure writing:
     */
    public function testWriteBadAttributeType()
    {
        $object = new XHTML($this->createMock('XMLWriter'));
        $object->write(['div', 'BadAttribs', 'b']);
    }

    /**
     * @dataProvider             providerWriteBadChild
     * @expectedException        \LogicException
     * @expectedExceptionMessage Failure writing:
     */
    public function testWriteBadChild($xhtml)
    {
        $object = new XHTML($this->createMock('XMLWriter'));
        $object->write($xhtml);
    }

    /**
     * @expectedException              \TypeError
     * @expectedExceptionMessageRegExp /^Argument 2 passed to.*must be of the type array, string given/
     */
    public function testWriteBadChildAttributeType()
    {
        try {
            $object = new XHTML($this->createMock('XMLWriter'));
            $object->write([
                'div',
                [],
                [
                    ['span', 'BAD', ['Count of span is correct at 3']]
                ]
            ]);
        } catch (\LogicException $thrown) {
            throw $thrown->getPrevious();
        }
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Bad children:
     */
    public function testWriteBadChildren()
    {
        try {
            $object = new XHTML($this->createMock('XMLWriter'));
            $object->write(['div', [], 123]);
        } catch (\LogicException $thrown) {
            throw $thrown->getPrevious();
        }
    }

    /**
     * @dataProvider             providerWriteBadRoot
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Bad root element
     */
    public function testWriteBadRoot($xml)
    {
        $object = new XHTML($this->createMock('XMLWriter'));
        $object->write($xml);
    }

    /**
     * @expectedException              \TypeError
     * @expectedExceptionMessageRegExp /^Argument 1 passed to.*must be of the type string, null given/
     */
    public function testWriteBadTagType()
    {
        try {
            $object = new XHTML($this->createMock('XMLWriter'));
            $object->write([null, 'a', 'b']);
        } catch (LogicException $thrown) {
            throw $thrown->getPrevious();
        }
    }

    /**
     * Writing an inline element turns off indenting during the write.
     */
    public function testWriteXHTMLInlineIndent()
    {
        $xIndex    = 3;
        $xmlWriter = $this->createMock('XMLWriter');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('setIndent')
            ->with(false);
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
            ->with(true);

        $object = new XHTML($xmlWriter);
        $object->write(['pre', ['A' => 'Val'], "This text\nis inline\nOK\n"]);
    }

    public function testWriteStart()
    {
        $xIndex    = 0;
        $xmlWriter = $this->createMock('XMLWriter');
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
            ->with('html', '-//W3C//DTD XHTML 1.1//EN', 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd');
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

        $object = new XHTML($xmlWriter);
        $object->writeStart();
    }
}
// EOF
