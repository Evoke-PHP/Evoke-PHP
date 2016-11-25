<?php
namespace Evoke_Test\View\HTML5;

use Evoke\View\HTML5\FormBuilder;
use PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\View\HTML5\FormBuilder
 */
class FormBuilderTest extends PHPUnit_Framework_TestCase
{
    /*********/
    /* Tests */
    /*********/

    /**
     * Ensure that a form can be built by adding generic elements to it.
     */
    public function testAddElement()
    {
        $formData = [
            ['div', ['class' => 'testAddElement'], 'Added'],
            ['div', ['class' => 'Another'], 'Done']
        ];

        $object = new FormBuilder(['action' => '/yodude', 'method' => 'GET']);

        $object->add($formData[0]);
        $object->add($formData[1]);

        $this->assertSame(['form', ['action' => '/yodude', 'method' => 'GET'], $formData], $object->get());
    }

    /**
     * Ensure that a file input can be added to the form.
     */
    public function testAddFile()
    {
        $object = new FormBuilder;
        $object->addFile('filename', ['class' => 'Special']);

        $this->assertSame(
            [
                'form',
                ['action' => '', 'method' => 'POST'],
                [
                    [
                        'input',
                        [
                            'name'  => 'filename',
                            'type'  => 'file',
                            'class' => 'Special'
                        ]
                    ]
                ]
            ],
            $object->get()
        );
    }

    /**
     * Ensure that a hidden input can be added to the form.
     */
    public function testAddHidden()
    {
        $object = new FormBuilder;
        $object->addHidden('nameField', 'valueField');

        $this->assertSame(
            [
                'form',
                ['action' => '', 'method' => 'POST'],
                [
                    [
                        'input',
                        [
                            'name'  => 'nameField',
                            'type'  => 'hidden',
                            'value' => 'valueField'
                        ]
                    ]
                ]
            ],
            $object->get()
        );
    }

    /**
     * Ensure that a generic input can be added to the form.
     */
    public function testAddInput()
    {
        $object = new FormBuilder;
        $object->addInput(['class' => 'Special', 'type' => 'generic'], 'Val');

        $this->assertSame(
            [
                'form',
                ['action' => '', 'method' => 'POST'],
                [
                    [
                        'input',
                        ['class' => 'Special', 'type' => 'generic', 'value' => 'Val']
                    ]
                ]
            ],
            $object->get()
        );
    }

    /**
     * Ensure that a label can be added to the form.
     */
    public function testAddLabel()
    {
        $object = new FormBuilder;
        $object->addLabel('forField', 'textField');

        $this->assertSame(
            [
                'form',
                ['action' => '', 'method' => 'POST'],
                [['label', ['for' => 'forField'], 'textField']]
            ],
            $object->get()
        );
    }

    public function testAddRadio()
    {
        $object = new FormBuilder;
        $object->addRadio('Group', 'Val');

        $this->assertSame(
            [
                'form',
                ['action' => '', 'method' => 'POST'],
                [
                    [
                        'input',
                        [
                            'name'  => 'Group',
                            'type'  => 'radio',
                            'value' => 'Val']
                    ]
                ]
            ],
            $object->get()
        );
    }

    public function testAddSelect()
    {
        $object = new FormBuilder;
        $object->addSelect(
            'Colour',
            [
                'Red'   => 1,
                'Blue'  => 2,
                'Green' => 3
            ]
        );

        $this->assertSame(
            [
                'form',
                ['action' => '', 'method' => 'POST'],
                [
                    [
                        'select',
                        [
                            'id'   => 'Colour',
                            'name' => 'Colour'
                        ],
                        [
                            ['option', ['value' => 1], 'Red'],
                            ['option', ['value' => 2], 'Blue'],
                            ['option', ['value' => 3], 'Green']
                        ]
                    ]
                ]
            ],
            $object->get()
        );
    }

    /**
     * Ensure that a submit input can be added to the form.
     */
    public function testAddSubmit()
    {
        $object = new FormBuilder;
        $object->addSubmit('nameField', 'valueField');

        $this->assertSame(
            [
                'form',
                ['action' => '', 'method' => 'POST'],
                [
                    [
                        'input',
                        [
                            'name'  => 'nameField',
                            'type'  => 'submit',
                            'value' => 'valueField'
                        ]
                    ]
                ]
            ],
            $object->get()
        );
    }

    /**
     * Ensure that a text input can be added to the form.
     */
    public function testAddText()
    {
        $object = new FormBuilder;
        $object->addText('nameField', 'valueField', 47, ['class' => 'Special']);

        $this->assertSame(
            [
                'form',
                ['action' => '', 'method' => 'POST'],
                [
                    [
                        'input',
                        [
                            'class'  => 'Special',
                            'length' => 47,
                            'name'   => 'nameField',
                            'type'   => 'text',
                            'value'  => 'valueField'
                        ]
                    ]
                ]
            ],
            $object->get()
        );
    }

    /**
     * Ensure that a textarea can be added to the form.
     */
    public function testAddTextArea()
    {
        $object = new FormBuilder;
        $object->addTextArea('nameField', 'valueField', 85, 7, ['class' => 'Special']);

        $this->assertSame(
            [
                'form',
                ['action' => '', 'method' => 'POST'],
                [
                    [
                        'textarea',
                        [
                            'class' => 'Special',
                            'cols'  => 7,
                            'name'  => 'nameField',
                            'rows'  => 85
                        ],
                        'valueField'
                    ]
                ]
            ],
            $object->get()
        );
    }

    /**
     * @expectedException LogicException
     */
    public function testLogicCannotAddRowWithinARow()
    {
        $object = new FormBuilder;
        $object->startRow();
        $object->addRow('Bad');
    }

    /**
     * @expectedException LogicException
     */
    public function testLogicCannotHaveIncompleteRow()
    {
        $object = new FormBuilder;
        $object->startRow();
        $object->get();
    }

    /**
     * @expectedException LogicException
     */
    public function testLogicStartRowCannotBeNested()
    {
        $object = new FormBuilder;
        $object->startRow();
        $object->startRow();
    }

    /**
     * @expectedException LogicException
     */
    public function testLogicCannotFinishUnstartedRow()
    {
        $object = new FormBuilder;
        $object->finishRow();
    }

    /**
     * Ensure that the form builder can be reset.
     */
    public function testReset()
    {
        $object = new FormBuilder;
        $object->addTextArea('nameField', 'valueField', 85, 7, ['class' => 'Special']);
        $object->reset();

        $this->assertSame(
            [
                'form',
                ['action' => '', 'method' => 'POST'],
                []
            ],
            $object->get()
        );
    }

    /**
     * Ensure that a reset can be done even if a row is started.
     */
    public function testResetEvenIfRowStarted()
    {
        $object = new FormBuilder;
        $object->addTextArea('nameField', 'valueField', 85, 7, ['class' => 'Special']);
        $object->startRow();
        $object->reset();

        $this->assertSame(
            [
                'form',
                ['action' => '', 'method' => 'POST'],
                []
            ],
            $object->get()
        );
    }

    /**
     * Test row can be added.
     */
    public function testRowAdd()
    {
        $object = new FormBuilder;
        $object->startRow();
        $object->add(['div', [], 'A']);
        $object->finishRow();

        $this->assertSame(
            [
                'form',
                ['action' => '', 'method' => 'POST'],
                [
                    [
                        'div',
                        ['class' => 'row'],
                        [['div', [], 'A']]
                    ]
                ]
            ],
            $object->get()
        );
    }

    /**
     * Test multiple items can be added to a row.
     */
    public function testRowMultipleAdd()
    {
        $object = new FormBuilder;
        $object->startRow();
        $object->add(['div', [], 'A']);
        $object->add(['div', ['class' => 'B'], 'B']);
        $object->finishRow();

        $this->assertSame(
            [
                'form',
                ['action' => '', 'method' => 'POST'],
                [
                    [
                        'div',
                        ['class' => 'row'],
                        [
                            ['div', [], 'A'],
                            ['div', ['class' => 'B'], 'B']
                        ]
                    ]
                ]
            ],
            $object->get()
        );
    }

    /**
     * Test multiple rows can be added to a form.
     */
    public function testRowMultiples()
    {
        $object = new FormBuilder;
        $object->startRow();
        $object->add(['div', [], 'A']);
        $object->add(['div', ['class' => 'B'], 'B']);
        $object->finishRow();
        $object->startRow();
        $object->add(['span', [], 'C']);
        $object->finishRow();

        $this->assertSame(
            [
                'form',
                ['action' => '', 'method' => 'POST'],
                [
                    [
                        'div',
                        ['class' => 'row'],
                        [
                            ['div', [], 'A'],
                            ['div', ['class' => 'B'], 'B']
                        ]
                    ],
                    [
                        'div',
                        ['class' => 'row'],
                        [['span', [], 'C']]
                    ]
                ]
            ],
            $object->get()
        );

    }

    /**
     * Ensure that a form can have it's action set.
     */
    public function testSetAction()
    {
        $object = new FormBuilder;
        $object->setAction('/Test/Value');
        $object->add(['div', [], 'Non-Empty-Form']);

        $this->assertSame(
            [
                'form',
                ['action' => '/Test/Value', 'method' => 'POST'],
                [['div', [], 'Non-Empty-Form']]
            ],
            $object->get()
        );
    }

    public function testSetAttributes()
    {
        $object = new FormBuilder();
        $object->setAttributes([
            'action' => '/Magic',
            'class'  => 'A',
            'method' => 'GET'
        ]);

        $this->assertSame(
            [
                'form',
                [
                    'action' => '/Magic',
                    'class'  => 'A',
                    'method' => 'GET'
                ],
                []
            ],
            $object->get()
        );
    }

    /**
     * Ensure that a form can have it's method set.
     */
    public function testSetMethod()
    {
        $object = new FormBuilder;
        $object->setMethod('PUT');
        $object->add(['div', [], 'Non-Empty-Form']);

        $this->assertSame(
            [
                'form',
                ['action' => '', 'method' => 'PUT'],
                [['div', [], 'Non-Empty-Form']]
            ],
            $object->get()
        );
    }
}
// EOF
