<?php
namespace Evoke_Test\View\XHTML;

use Evoke\View\XHTML\FormBuilder,
    PHPUnit_Framework_TestCase;

/**
 *  @covers Evoke\View\XHTML\FormBuilder
 */
class FormBuilderTest extends PHPUnit_Framework_TestCase
{
	/*********/
	/* Tests */
	/*********/

	/**
	 * Ensure that a form can be built by adding generic elements to it.
	 *
	 * @covers Evoke\View\XHTML\FormBuilder::__construct
	 * @covers Evoke\View\XHTML\FormBuilder::add
	 * @covers Evoke\View\XHTML\FormBuilder::get
	 */
	public function testAddElement()
	{
		$formData = [['div', ['class' => 'testAddElement'], 'Added'],
		             ['div', ['class' => 'Another'], 'Done']];
		
		$object = new FormBuilder(['action' => '/yodude', 'method' => 'GET']);

		$object->add($formData[0]);
		$object->add($formData[1]);

		$this->assertSame(
			['form', ['action' => '/yodude', 'method' => 'GET'], $formData],
			$object->get());
	}

	/**
	 * Ensure that a file input can be added to the form.
	 *
	 * @covers Evoke\View\XHTML\FormBuilder::addFile
	 */
	public function testAddFile()
	{
		$object = new FormBuilder;
		$object->addFile('filename', ['class' => 'Special']);

		$this->assertSame(
			['form',
			 ['action' => '', 'method' => 'POST'],
			 [
				 ['input',
				  ['name' => 'filename',
				   'type' => 'file',
				   'class' => 'Special']]]],
			$object->get());
	}

	/**
	 * Ensure that a hidden input can be added to the form.
	 *
	 * @covers Evoke\View\XHTML\FormBuilder::addHidden
	 */
	public function testAddHidden()
	{
		$object = new FormBuilder;
		$object->addHidden('nameField', 'valueField');

		$this->assertSame(
			['form',
			 ['action' => '', 'method' => 'POST'],
			 [['input',
			   [
				   'name' => 'nameField',
				   'type' => 'hidden',
				   'value' => 'valueField']]]],
			$object->get());
	}
		
	/**
	 * Ensure that a generic input can be added to the form.
	 *
	 * @covers Evoke\View\XHTML\FormBuilder::addInput
	 */
	public function testAddInput()
	{
		$object = new FormBuilder;
		$object->addInput(['class' => 'Special', 'type' => 'generic'], 'Val');

		$this->assertSame(
			['form',
			 ['action' => '', 'method' => 'POST'],
			 [
				 ['input',
				  ['class' => 'Special', 'type' => 'generic'],
				  'Val']]],
			$object->get());
	}

	/**
	 * Ensure that a label can be added to the form.
	 *
	 * @covers Evoke\View\XHTML\FormBuilder::addLabel
	 */
	public function testAddLabel()
	{
		$object = new FormBuilder;
		$object->addLabel('forField', 'textField');

		$this->assertSame(
			['form',
			 ['action' => '', 'method' => 'POST'],
			 [['label', ['for' => 'forField'], 'textField']]],
			$object->get());
	}
		
	/**
	 * Ensure that a submit input can be added to the form.
	 *
	 * @covers Evoke\View\XHTML\FormBuilder::addSubmit
	 */
	public function testAddSubmit()
	{
		$object = new FormBuilder;
		$object->addSubmit('nameField', 'valueField');

		$this->assertSame(
			['form',
			 ['action' => '', 'method' => 'POST'],
			 [['input',
			   ['name'  => 'nameField',
			    'type'  => 'submit',
			    'value' => 'valueField']]]],
			$object->get());
	}

	/**
	 * Ensure that a text input can be added to the form.
	 *
	 * @covers Evoke\View\XHTML\FormBuilder::addText
	 */
	public function testAddText()
	{
		$object = new FormBuilder;
		$object->addText('nameField', 'valueField', 47, ['class' => 'Special']);

		$this->assertSame(
			['form',
			 ['action' => '', 'method' => 'POST'],
			 [['input',
			   ['class'  => 'Special',
			    'length' => 47,
			    'name'   => 'nameField',
			    'type'   => 'text',
			    'value'  => 'valueField']]]],
			$object->get());
	}
	
	/**
	 * Ensure that a textarea can be added to the form.
	 *
	 * @covers Evoke\View\XHTML\FormBuilder::addTextArea
	 */
	public function testAddTextArea()
	{
		$object = new FormBuilder;
		$object->addTextArea('nameField', 'valueField', 85, 7, ['class' => 'Special']);

		$this->assertSame(
			['form',
			 ['action' => '', 'method' => 'POST'],
			 [['textarea',
			   ['class'  => 'Special',
			    'cols'   => 7,
			    'name'   => 'nameField',
			    'rows'   => 85],
			   'valueField']]],
			$object->get());
	}
	
	/**
	 * Ensure that a form can have it's action set.
	 *
	 * @covers Evoke\View\XHTML\FormBuilder::setAction
	 */
	public function testSetAction()
	{
		$object = new FormBuilder;
		$object->setAction('/Test/Value');
		$object->add(['div', [], 'Non-Empty-Form']);

		$this->assertSame(
			['form',
			 ['action' => '/Test/Value', 'method' => 'POST'],
			 [['div', [], 'Non-Empty-Form']]],
			$object->get());
	}

	/**
	 * Ensure that a form can have it's method set.
	 *
	 * @covers Evoke\View\XHTML\FormBuilder::setMethod
	 */
	public function testSetMethod()
	{
		$object = new FormBuilder;
		$object->setMethod('PUT');
		$object->add(['div', [], 'Non-Empty-Form']);

		$this->assertSame(
			['form',
			 ['action' => '', 'method' => 'PUT'],
			 [['div', [], 'Non-Empty-Form']]],
			$object->get());
	}
}
// EOF