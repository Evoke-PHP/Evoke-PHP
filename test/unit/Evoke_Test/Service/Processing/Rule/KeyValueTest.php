<?php
/**
 * KeyValueTest
 *
 * @package   Evoke_Test\Service\Processing\Rule
 */

namespace Evoke_Test\Service\Processing\Rule;

use Evoke\Service\Processing\Rule\KeyValue;

class StubValueCallback
{
    protected $value;

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }
}

/**
 * @covers Evoke\Service\Processing\Rule\KeyValue
 */
class KeyValueTest extends \PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerMatchesWithKeyPresentOnly()
    {
        return [
            'Has_Key' => [
                'Data'     => ['A' => 1, 'B' => 2, 'C' => 3],
                'Expected' => true,
                'Key'      => 'B'
            ],
            'No_Key'  => [
                'Data'     => ['A' => 1, 'B' => 2, 'C' => 3],
                'Expected' => false,
                'Key'      => 'Missing'
            ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * @dataProvider providerMatchesWithKeyPresentOnly
     */
    public function testMatchesOnlyOnEmpty($data, $expected, $key)
    {
        $stubCallback = new StubCallback;
        $obj          = new KeyValue([$stubCallback, 'setArgs'], $key);
        $obj->setData($data);

        $this->assertSame($expected, $obj->isMatch());
    }

    public function testExecutesWithValueFromKey()
    {
        $stubValueCallback = new StubValueCallback;
        $obj               = new KeyValue([$stubValueCallback, 'setValue'], 'KEY');
        $obj->setData(['NOT' => 2, 'KEY' => 6]);
        $obj->execute();

        $this->assertSame(6, $stubValueCallback->getValue());
    }
}
// EOF