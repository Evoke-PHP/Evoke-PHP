<?php
namespace Evoke_Test\View\HTML5;

use Evoke\View\HTML5\Throwable;
use PHPUnit_Framework_TestCase;

class ThrowableTest extends PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    /**
     * Get the view.
     *
     * @covers Evoke\View\HTML5\Throwable::get
     * @covers Evoke\View\HTML5\Throwable::set
     */
    public function testGetView()
    {
        $error = new \Error('MESSAGE');
        $object = new Throwable;
        $object->set($error);
        $expected = [
            'div',
            ['class' => 'Throwable'],
            [
                ['div', ['class' => 'location'], 'Thrown at /home/pyoung/Coding/Evoke-PHP/test/unit/View/HTML5/ThrowableTest.php line 21'],
                ['pre', [], 'MESSAGE'],
            ]
        ];

        $actual = $object->get();
        unset($actual[2][2]);

        $this->assertSame($expected, $actual);
    }

    /**
     * Unset throwable causes throw.
     *
     * @covers            Evoke\View\HTML5\Throwable::get
     * @expectedException LogicException
     */
    public function testUnsetException()
    {
        $object = new Throwable;
        $object->get();
    }
}
// EOF
