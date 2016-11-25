<?php
/**
 * KeyOtherValueTest
 *
 * @package   Evoke_Test\Service\Processing\Rule
 */

namespace Evoke_Test\Service\Processing\Rule;

use Evoke\Service\Processing\Rule\KeyOtherValue;

/**
 * @covers Evoke\Service\Processing\Rule\KeyOtherValue
 */
class KeyOtherValueTest extends \PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerMatches()
    {
        return [
            'Data_Empty'        => [
                'Data'      => [],
                'Expected'  => false,
                'Key'       => '1',
                'Other_Key' => '2'
            ],
            'Has_Key_Has_Other' => [
                'Data'      => ['A' => 1, 'B' => 2, 'C' => 3],
                'Expected'  => true,
                'Key'       => 'B',
                'Other_Key' => 'A'
            ],
            'Has_Key_Not_Other' => [
                'Data'      => ['A' => 1, 'B' => 2, 'C' => 3],
                'Expected'  => false,
                'Key'       => 'B',
                'Other_Key' => 'D'
            ],
            'Not_Key_Has_Other' => [
                'Data'      => ['A' => 1, 'B' => 2, 'C' => 3],
                'Expected'  => false,
                'Key'       => 'D',
                'Other_Key' => 'A'
            ],
            'Not_Key_Not_Other' => [
                'Data'      => ['A' => 1, 'B' => 2, 'C' => 3],
                'Expected'  => false,
                'Key'       => 'D',
                'Other_Key' => 'E'
            ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    public function testExecute()
    {
        $stubCallback = new StubCallback;
        $obj          = new KeyOtherValue([$stubCallback, 'setArgs'], 'ONE', 'TWO');
        $obj->setData(['ONE' => 1, 'TWO' => 2, 'DC' => 3]);
        $obj->execute();

        $this->assertSame([2], $stubCallback->getArgs());
    }

    /**
     * @dataProvider providerMatches
     */
    public function testMatches($data, $expected, $key, $otherKey)
    {
        $stubCallback = new StubCallback;
        $obj          = new KeyOtherValue([$stubCallback, 'setArgs'], $key, $otherKey);
        $obj->setData($data);

        $this->assertSame($expected, $obj->isMatch());
    }
}
// EOF