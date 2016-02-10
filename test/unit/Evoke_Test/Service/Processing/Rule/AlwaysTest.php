<?php
/**
 * AlwaysTest
 *
 * @package   Evoke_Test\Service\Processing\Rule
 */
namespace Evoke_Test\Service\Processing\Rule;

use Evoke\Service\Processing\Rule\Always;

class StubAlways extends Always
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        echo __METHOD__ . "Don't Care, this should not be called.";
    }
}

/**
 * @covers Evoke\Service\Processing\Rule\Always
 */
class AlwaysTest extends \PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerMatchesAlways()
    {
        return [
            'Has_Data' => [
                'Data'     => ['A' => 1, 'B' => 2, 'C' => 3],
            ],
            'No_Data'  => [
                'Data'     => [],
            ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * @dataProvider providerMatchesAlways
     */
    public function testMatchesAlways($data)
    {
        $stubCallback = new StubCallback;
        $obj          = new StubAlways([$stubCallback, 'setArgs']);
        $obj->setData($data);

        $this->assertTrue($obj->isMatch());
    }
}
// EOF
