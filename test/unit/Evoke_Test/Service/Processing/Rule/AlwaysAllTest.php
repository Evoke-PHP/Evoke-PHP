<?php
/**
 * AlwaysAllTest
 *
 * @package   Evoke_Test\Service\Processing\Rule
 */
namespace Evoke_Test\Service\Processing\Rule;

use Evoke\Service\Processing\Rule\AlwaysAll;

class AlwaysAllTest extends \PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    public function testExecutesWithNoParameters()
    {
        $stubCallback = new StubCallback;
        $obj          = new AlwaysAll([$stubCallback, 'setArgs']);
        $obj->setData(['NOT' => 2, 'ANOTHER' => 6]);
        $obj->execute();

        $this->assertSame([['NOT' => 2, 'ANOTHER' => 6]], $stubCallback->getArgs());
    }
}
