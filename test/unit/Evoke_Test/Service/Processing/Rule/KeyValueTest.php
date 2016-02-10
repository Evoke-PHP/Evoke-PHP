<?php
/**
 * KeyValueTest
 *
 * @package   Evoke_Test\Service\Processing\Rule
 */

namespace Evoke_Test\Service\Processing\Rule;

use Evoke\Service\Processing\Rule\KeyValue;

/**
 * @covers Evoke\Service\Processing\Rule\KeyValue
 */
class KeyValueTest extends \PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    public function testExecutesWithValueFromKey()
    {
        $stubCallback = new StubCallback;
        $obj               = new KeyValue([$stubCallback, 'setArgs'], 'KEY');
        $obj->setData(['NOT' => 2, 'KEY' => 6]);
        $obj->execute();

        $this->assertSame([6], $stubCallback->getArgs());
    }
}
// EOF