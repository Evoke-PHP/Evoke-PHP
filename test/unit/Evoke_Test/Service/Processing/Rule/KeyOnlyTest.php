<?php
/**
 * KeyOnlyTest
 *
 * @package   Evoke_Test\Service\Processing\Rule
 */

namespace Evoke_Test\Service\Processing\Rule;

use Evoke\Service\Processing\Rule\KeyOnly;

/**
 * @covers Evoke\Service\Processing\Rule\KeyOnly
 */
class KeyOnlyTest extends \PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    public function testExecutesWithNoParams()
    {
        $stubCallback = new StubCallback;
        $obj          = new KeyOnly([$stubCallback, 'setArgs'], 'KEY');
        $obj->setData(['NOT' => 2, 'KEY' => 6]);
        $obj->execute();

        $this->assertSame([], $stubCallback->getArgs());
    }
}
// EOF