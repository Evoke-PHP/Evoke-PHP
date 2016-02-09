<?php
namespace Evoke_Test\View\HTML5;

use Evoke\View\HTML5\Exception;
use PHPUnit_Framework_TestCase;

class ExceptionTest extends PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    /**
     * Get the view.
     *
     * @covers Evoke\View\HTML5\Exception::get
     * @covers Evoke\View\HTML5\Exception::set
     */
    public function testGetView()
    {
        $testException = new \Exception('Created in test.');
        $object        = new Exception;
        $object->set($testException);
        $expected = [
            'div',
            ['class' => 'Exception'],
            [
                ['div', ['class' => 'Type'], 'Exception'],
                ['p', ['class' => 'Message'], 'Created in test.'],
                [
                    'pre',
                    ['class' => 'Trace'],
                    $testException->getTraceAsString()
                ]
            ]
        ];

        $this->assertSame($expected, $object->get());
    }

    /**
     * Unset exception causes throw.
     *
     * @covers            Evoke\View\HTML5\Exception::get
     * @expectedException LogicException
     */
    public function testUnsetException()
    {
        $object = new Exception;
        $object->get();
    }
}
// EOF
