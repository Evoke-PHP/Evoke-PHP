<?php
/**
 * AlwaysNoneTest
 *
 * @package   Evoke_Test\Service\Processing\Rule
 */

namespace Evoke_Test\Service\Processing\Rule;

use Evoke\Service\Processing\Rule\AlwaysNone;

class AlwaysNoneTest extends \PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    public function testExecutesWithNoParameters()
    {
        $stubCallback = new StubCallback;
        $obj          = new AlwaysNone([$stubCallback, 'setArgs']);
        $obj->setData(['NOT' => 2, 'ANOTHER' => 6]);
        $obj->execute();

        $this->assertSame([], $stubCallback->getArgs());
    }
}
