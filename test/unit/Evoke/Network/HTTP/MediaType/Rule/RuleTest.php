<?php
namespace Evoke_Test\Network\HTTP\MediaType\Rule;

use Evoke\Network\HTTP\MediaType\Rule\Rule;
use PHPUnit_Framework_TestCase;

class TestRuleExtended extends Rule
{
    public function getMediaType()
    {
        return $this->mediaType;
    }

    public function isMatch()
    {
        return true;
    }
}

/**
 * @covers Evoke\Network\HTTP\MediaType\Rule\Rule
 */
class RuleTest extends PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    public function testCreate()
    {
        $obj = new TestRuleExtended('Output_Format');
        $this->assertInstanceOf('Evoke\Network\HTTP\MediaType\Rule\Rule', $obj);
    }

    public function testGetOutputFormat()
    {
        $obj = new TestRuleExtended('Output_Format');
        $this->assertSame('Output_Format', $obj->getOutputFormat());
    }

    public function testSetMediaType()
    {
        $obj       = new TestRuleExtended('DC');
        $mediaType = [
            'Params'   => [],
            'Q_Factor' => '1.0',
            'Subtype'  => 'TV',
            'Type'     => 'Screen'
        ];
        $obj->setMediaType($mediaType);
        $this->assertSame($mediaType, $obj->getMediaType());
    }
}
// EOF
