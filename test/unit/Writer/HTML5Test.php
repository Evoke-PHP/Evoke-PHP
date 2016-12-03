<?php
namespace Evoke_Test\Writer;

use Evoke\Writer\HTML5;
use PHPUnit_Framework_TestCase;

/**
 * @covers       Evoke\Writer\HTML5
 */
class HTML5Test extends PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    public function testWriteStart()
    {
        $xIndex    = 3;
        $xmlWriter = $this->createMock('XMLWriter');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('startDTD')
            ->with('html');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('endDTD');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('startElement')
            ->with('html');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('writeAttribute')
            ->with('class', 'no-js');
        $xmlWriter
            ->expects($this->at($xIndex++))
            ->method('writeAttribute')
            ->with('lang', 'EN');

        $object = new HTML5($xmlWriter);
        $object->writeStart();
    }
}
// EOF
