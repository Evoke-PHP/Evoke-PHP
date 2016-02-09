<?php
/**
 * BlankTest
 *
 * @package   Evoke_Test\Network\URI\Processing\Rule
 */
namespace Evoke_Test\Network\URI\Processing\Rule;

use Evoke\Network\URI\Processing\Rule\Blank;

/**
 * @covers Evoke\Network\URI\Processing\Rule\Blank
 */
class BlankTest extends \PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    public function providerMatchesOnlyOnEmpty()
    {
        return [
            'Is_Empty'  => [
                'Data'     => [],
                'Expected' => true
            ],
            'Not_Empty' => [
                'Data'     => ['Got_Some'],
                'Expected' => false
            ]
        ];
    }

    /*********/
    /* Tests */
    /*********/

    /**
     * @dataProvider providerMatchesOnlyOnEmpty
     */
    public function testMatchesOnlyOnEmpty($data, $expected)
    {
        $stubCallback = new StubCallback;
        $obj = new Blank([$stubCallback, 'setArgs']);
        $obj->setData($data);

        $this->assertSame($expected, $obj->isMatch());
    }

    public function testExecutesWithEmpty()
    {
        $stubCallback = new StubCallback;
        $obj = new Blank([$stubCallback, 'setArgs']);
        $obj->setData([]);
        $obj->execute();

        $this->assertSame([], $stubCallback->getArgs());
        $this->assertSame([[]], $stubCallback->getArgsStack());
    }
}
