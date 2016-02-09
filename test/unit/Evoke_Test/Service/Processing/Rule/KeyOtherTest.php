<?php
/**
 * KeyOtherTest
 *
 * @package   Evoke_Test\Service\Processing\Rule
 */

namespace Evoke_Test\Service\Processing\Rule;

use Evoke\Service\Processing\Rule\KeyOther;

/**
 * @covers Evoke\Service\Processing\Rule\KeyOther
 */
class KeyOtherTest extends \PHPUnit_Framework_TestCase
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
        $obj          = new KeyOther([$stubCallback, 'setArgs'], $key);
        $obj->setData($data);

        $this->assertSame($expected, $obj->isMatch());
    }

    public function testExecutesWithValueFromKey()
    {
        $stubCallback = new StubCallback;
        $obj               = new KeyOther([$stubCallback, 'setArgs'], 'KEY');
        $obj->setData(['NOT' => 2, 'KEY' => 6, 'Other_Not' => 'Other']);
        $obj->execute();

        $this->assertSame([['NOT' => 2, 'Other_Not' => 'Other']], $stubCallback->getArgs());
    }
}
