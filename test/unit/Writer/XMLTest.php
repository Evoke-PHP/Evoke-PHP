<?php
namespace Evoke_Test\Writer;

use Evoke\Writer\XML;
use PHPUnit_Framework_TestCase;

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
        return [
            'indent_on'       => [true],
            'indent_off'      => [false],
            'indent_specific' => [true, '        ']
        ];
    }

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
                            ['b', [], 'Good'],
                            ['c', 'BAD']
                        ]
                    ]
            ]
        ];
    }

    public function providerWriteBadRoot()
    {
        return [
            'Array_Too_Many' => ['xml' => ['1', '2', '3', '4']],
            'Non-Array'      => ['xml' => 'non_array']

        ];
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * Create the object.
     *
     * @dataProvider providerConstruct
     */
    public function testConstruct($indent, $indentString = '    ')
    {
        $xIndex    = 0;
        $xmlWriter = $this->createMock('XMLWriter');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('openMemory');

        if ($indent) {
            $xmlWriter
                ->expects($this->at($xIndex++))
                ->method('setIndentString')
                ->with($indentString);
            $xmlWriter
                ->expects($this->at($xIndex++))
                ->method('setIndent')
                ->with(true);
        }

        $object = new XML($xmlWriter, $indent, $indentString);
        $this->assertInstanceOf('Evoke\Writer\XML', $object);
    }

    /**
     * Converts to a string.
     */
    public function testConvertsToAString()
    {
        $xIndex    = 0;
        $xmlWriter = $this->createMock('XMLWriter');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('openMemory');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('outputMemory')
            ->with(false)
            ->will($this->returnValue('Whatever'));

        $object = new XML($xmlWriter, false);
        $this->assertSame('Whatever', (string)$object);
    }

    public function testCleanable()
    {
        $xIndex    = 0;
        $xmlWriter = $this->createMock('XMLWriter');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('openMemory');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('outputMemory')
            ->with(true);
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('outputMemory')
            ->with(false)
            ->will($this->returnValue(''));

        $object = new XML($xmlWriter, false);
        $object->clean();
        $this->assertSame('', (string)$object);
    }

    public function testFlush()
    {
        $xIndex    = 0;
        $xmlWriter = $this->createMock('XMLWriter');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('openMemory');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('outputMemory')
            ->with(true)
            ->will($this->returnValue('Flush Whatever'));
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('outputMemory')
            ->with(false)
            ->will($this->returnValue(''));

        $object = new XML($xmlWriter, false);
        ob_start();
        $object->flush();
        $flushed = ob_get_contents();
        ob_end_clean();

        $this->assertSame('Flush Whatever', $flushed);
        $this->assertSame('', (string)$object);

    }

    public function testWrite()
    {
        $expectedOutput = <<<XML
<library location="bedroom">
    <book>
        <title>Self help 101</title>
        <author>self</author>
    </book>
    <book type="hardcover">
        <title>Fix it after you broke it</title>
        <author>Needs help</author>
    </book>
</library>

XML
        ;
        $object         = new XML(new \XMLWriter);
        $object->write([
            'library',
            ['location' => 'bedroom'],
            [
                [
                    'book',
                    [],
                    [
                        ['title', [], 'Self help 101'],
                        ['author', [], 'self']
                    ]
                ],
                [
                    'book',
                    ['type' => 'hardcover'],
                    [
                        ['title', [], 'Fix it after you broke it'],
                        ['author', [], 'Needs help']
                    ]
                ]
            ]
        ]);
        $this->assertSame($expectedOutput, (string)($object));
    }

    /**
     * @expectedException        \LogicException
     * @expectedExceptionMessage Failure writing:
     */
    public function testWriteBadAttributeType()
    {
        $object = new XML($this->createMock('XMLWriter'));
        $object->write(['book', 'BadAttribs', 'b']);
    }

    /**
     * @dataProvider             providerWriteBadChild
     * @expectedException        \LogicException
     * @expectedExceptionMessage Failure writing:
     */
    public function testWriteBadChild($xml)
    {
        $object = new XML($this->createMock('XMLWriter'));
        $object->write($xml);
    }

    /**
     * @expectedException              \TypeError
     * @expectedExceptionMessageRegExp /^Argument 2 passed to.*must be of the type array, string given/
     */
    public function testWriteBadChildAttributeType()
    {
        try {
            $object = new XML($this->createMock('XMLWriter'));
            $object->write([
                'book',
                [],
                [
                    ['author', 'BAD', ['Count of author is correct at 3']]
                ]
            ]);
        } catch (\LogicException $thrown) {
            throw $thrown->getPrevious();
        }
    }

    /**
     * @expectedException        \TypeError
     * @expectedExceptionMessage Argument 1 passed to Evoke\Writer\XML::writeXMLChildren() must be of the type array, integer given
     */
    public function testWriteBadChildren()
    {
        try {
            $object = new XML($this->createMock('XMLWriter'));
            $object->write(['book', [], 123]);
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
        $object = new XML($this->createMock('XMLWriter'));
        $object->write($xml);
    }

    /**
     * @expectedException              \TypeError
     * @expectedExceptionMessageRegExp /^Argument 1 passed to.*must be of the type string, integer given/
     */
    public function testWriteBadTagType()
    {
        try {
            $object = new XML($this->createMock('XMLWriter'));
            $object->write([241, 'a', 'b']);
        } catch (\LogicException $thrown) {
            throw $thrown->getPrevious();
        }
    }

    public function testWriteTagOnly()
    {
        $expectedOutput = '<book/>';
        $object         = new XML(new \XMLWriter, false);
        $object->write(['book']);
        $this->assertSame($expectedOutput, (string)($object));
    }

    public function testWriteTagAndAttributesOnly()
    {
        $expectedOutput = '<book auth="Smith"/>';
        $object         = new XML(new \XMLWriter, false);
        $object->write(['book', ['auth' => 'Smith']]);
        $this->assertSame($expectedOutput, (string)($object));
    }

    public function testWriteEnd()
    {
        $xIndex    = 0;
        $xmlWriter = $this->createMock('XMLWriter');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('openMemory');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('endDocument')
            ->with();

        $object = new XML($xmlWriter, false);
        $object->writeEnd();
    }

    public function testWriteStart()
    {
        $xIndex    = 3;
        $xmlWriter = $this->createMock('XMLWriter');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('startDocument')
            ->with('1.0', 'UTF-8');

        $object = new XML($xmlWriter, 'XML');
        $object->writeStart();
    }
}
// EOF
