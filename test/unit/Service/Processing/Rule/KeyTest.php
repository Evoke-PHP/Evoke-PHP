<?php
/**
 * KeyTest
 *
 * @package   Evoke_Test\Service\Processing\Rule
 */
namespace Evoke_Test\Service\Processing\Rule;

use Evoke\Service\Processing\Rule\Key;

class StubKey extends Key
{
    public function execute()
    {
        echo __METHOD__ . "Don't Care, this should not be called.";
    }
}

/**
 * @covers Evoke\Service\Processing\Rule\Key
 */
class KeyTest extends \PHPUnit_Framework_TestCase
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
    public function testMatchesWithKeyPresentOnly($data, $expected, $key)
    {
        $stubCallback = new StubCallback;
        $obj          = new StubKey([$stubCallback, 'setArgs'], $key);
        $obj->setData($data);

        $this->assertSame($expected, $obj->isMatch());
    }
}
// EOF
