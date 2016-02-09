<?php
namespace Evoke_Test\View\HTML5;

use Evoke\View\HTML5\Error;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\View\HTML5\Error
 */
class ErrorTest extends PHPUnit_Framework_TestCase
{
    /******************/
    /* Data Providers */
    /******************/

    /*********/
    /* Tests */
    /*********/

    /**
     * Create an object.
     */
    public function testCreate()
    {
        $object = new Error;
        $this->assertInstanceOf('Evoke\View\HTML5\Error', $object);
    }

    /**
     * Get the view of an error.
     */
    public function testGetView()
    {
        $object = new Error('<UNK>');
        $object->set([
            'file' => 'FILE',
            'line' => 245,
            'type' => E_USER_ERROR
        ]);

        $this->assertSame(
            [
                'div',
                ['class' => 'Error'],
                [
                    [
                        'div',
                        ['class' => 'Details'],
                        [
                            ['span', ['class' => 'Type'], 'E_USER_ERROR'],
                            ['span', ['class' => 'File'], 'FILE'],
                            ['span', ['class' => 'Line'], 245]
                        ]
                    ],
                    ['p', ['class' => 'Message'], '<UNK>']
                ]
            ],
            $object->get()
        );
    }

    /**
     * Unknown errors can still be dealt with.
     */
    public function testUnknownError()
    {
        $object = new Error('WHO KNOWS');
        $object->set([
            'file'    => 'F',
            'line'    => 2,
            'message' => 'BLAH',
            'type'    => -1
        ]);

        $this->assertSame(
            [
                'div',
                ['class' => 'Error'],
                [
                    [
                        'div',
                        ['class' => 'Details'],
                        [
                            ['span', ['class' => 'Type'], 'WHO KNOWS'],
                            ['span', ['class' => 'File'], 'F'],
                            ['span', ['class' => 'Line'], 2]
                        ]
                    ],
                    ['p', ['class' => 'Message'], 'BLAH']
                ]
            ],
            $object->get()
        );
    }

    /**
     * If the error has not been set then it throws.
     *
     * @expectedException LogicException
     */
    public function testUnsetError()
    {
        $object = new Error;
        $object->get();
    }
}
// EOF
