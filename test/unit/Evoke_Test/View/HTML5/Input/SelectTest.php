<?php
namespace Evoke_Test\View\HTML5\Input;

use Evoke\View\HTML5\Input\Select;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\View\HTML5\Input\Select
 */
class SelectTest extends PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    /**
     * Create an object.
     */
    public function testCreate()
    {
        $object = new Select('Text_Field');
        $this->assertInstanceOf('Evoke\View\HTML5\Input\Select', $object);
    }

    /**
     * Get a select element.
     */
    public function testGetSelect()
    {
        $data   = [
            [
                'text_field' => 'One',
                'id_field'   => 1
            ],
            [
                'text_field' => 'Two',
                'id_field'   => 2
            ]
        ];
        $object = new Select(
            'text_field',
            'id_field',
            ['Attrib' => 'Main'],
            ['Attrib' => 'Option']
        );
        $object->setOptions($data);
        $this->assertSame(
            [
                'select',
                ['Attrib' => 'Main'],
                [
                    ['option', ['Attrib' => 'Option', 'value' => 1], 'One'],
                    ['option', ['Attrib' => 'Option', 'value' => 2], 'Two']
                ]
            ],
            $object->get()
        );
    }

    /**
     * We can have an option selected.
     */
    public function testGetOptionSelected()
    {
        $data = [
            [
                'text_field' => 'One',
                'id_field'   => 1
            ],
            [
                'text_field' => 'Two',
                'id_field'   => 2
            ]
        ];

        $object = new Select(
            'text_field',
            'id_field',
            ['Attrib' => 'Main'],
            ['Attrib' => 'Option']
        );
        $object->setSelected(2);
        $object->setOptions($data);
        $this->assertSame(
            [
                'select',
                ['Attrib' => 'Main'],
                [
                    ['option', ['Attrib' => 'Option', 'value' => 1], 'One'],
                    [
                        'option',
                        ['Attrib' => 'Option', 'value' => 2, 'selected' => 'selected'],
                        'Two'
                    ]
                ]
            ],
            $object->get()
        );
    }

    /**
     * @expectedException                InvalidArgumentException
     * @expectedExceptionMessage needs traversable options.
     */
    public function testNonTraversableOptionsAreInvalid()
    {
        $obj = new Select('T_Field');
        $obj->setOptions("Non Traversable");
    }

    /**
     * Unset Text Field throws.
     *
     * @expectedException LogicException
     */
    public function testUnsetText()
    {
        $data = [
            [
                'text_field' => 'One',
                'id_field'   => 1
            ],
            [
                'text_field' => 'Two',
                'id_field'   => 2
            ]
        ];

        $object = new Select('T_Field');
        $object->setOptions($data);
        $object->get();
    }

    /**
     * Unset Value Field throws.
     *
     * @expectedException LogicException
     */
    public function testUnsetValue()
    {
        $data = [
            [
                'text_field' => 'One',
                'id_field'   => 1
            ],
            [
                'text_field' => 'Two',
                'id_field'   => 2
            ]
        ];

        $object = new Select('text_field', 'value_field');
        $object->setOptions($data);
        $object->get();
    }
}
// EOF
