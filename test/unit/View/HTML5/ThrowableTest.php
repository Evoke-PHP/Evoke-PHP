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
                ['div', ['class' => 'location'], 'Thrown at ' . __FILE__ . ' line 21'],
                ['pre', [], 'MESSAGE'],

            ]
        ];

        $actual = $object->get();
        $actualMatchable = ['div', ['class' => 'Throwable'], [$actual[2][0], $actual[2][1]]];

        $this->assertSame($expected, $actualMatchable);
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
