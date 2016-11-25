<?php
namespace Evoke_Test\View\HTML5;

use Evoke\View\HTML5\MessageBox;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\View\HTML5\MessageBox
 */
class MessageBoxTest extends PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    /**
     * Create an object.
     */
    public function testCreate()
    {
        $object = new MessageBox;
        $this->assertInstanceOf('Evoke\View\HTML5\MessageBox', $object);
    }

    /**
     * Build a message box and get it.
     */
    public function testBuildAndGet()
    {
        $object = new MessageBox(['class' => 'test message_box info']);
        $object->setTitle('Test Box');
        $object->addContent(['div', [], 'One']);
        $object->addContent('Text');

        $this->assertSame(
            [
                'div',
                ['class' => 'test message_box info'],
                [
                    ['div', ['class' => 'title'], 'Test Box'],
                    [
                        'div',
                        ['class' => 'content'],
                        [
                            ['div', [], 'One'],
                            'Text'
                        ]
                    ]
                ]
            ],
            $object->get()
        );
    }
}
// EOF
