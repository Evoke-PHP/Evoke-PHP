<?php
namespace Evoke_Test\View\XHTML\Input;

use Evoke\View\XHTML\Input\Select,
	PHPUnit_Framework_TestCase;

/**
 * @covers Evoke\View\XHTML\Input\Select
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
		$this->assertInstanceOf('Evoke\View\XHTML\Input\Select', $object);
	}

	/**
	 * Get a select element.
	 */
	public function testGetSelect()
	{
		$data = [
			['Text_Field' => 'One',
			 'ID_Field'   => 1],
			['Text_Field' => 'Two',
			 'ID_Field'   => 2]];
		$object = new Select('Text_Field',
		                     'ID_Field',
		                     ['Attrib' => 'Main'],
		                     ['Attrib' => 'Option']);
		$object->setOptions($data);
		$this->assertSame(
			['select',
			 ['Attrib' => 'Main'],
			 [['option', ['Attrib' => 'Option', 'value' => 1], 'One'],
			  ['option', ['Attrib' => 'Option', 'value' => 2], 'Two']]],
			$object->get());
	}

	/**
	 * We can have an option selected.
	 */
	public function testGetOptionSelected()
	{
		$data = [
			['Text_Field' => 'One',
			 'ID_Field'   => 1],
			['Text_Field' => 'Two',
			 'ID_Field'   => 2]];

		$object = new Select('Text_Field',
		                     'ID_Field',
		                     ['Attrib' => 'Main'],
		                     ['Attrib' => 'Option']);
		$object->setSelected(2);
		$object->setOptions($data);
		$this->assertSame(
			['select',
			 ['Attrib' => 'Main'],
			 [['option', ['Attrib' => 'Option', 'value' => 1], 'One'],
			  ['option',
			   ['Attrib' => 'Option', 'value' => 2, 'selected' => 'selected'],
			   'Two']]],
			$object->get());
	}

	/** 
	 * @expectedException 		 InvalidArgumentException
	 * @expectedExceptionMessage needs options to be valid XHTML
	 */
	public function testEmptyOptionsAreNotValidXHTML()
	{
		$obj = new Select('T_Field');
		$obj->setOptions([]);
	}

	/** 
	 * @expectedException 		 InvalidArgumentException
	 * @expectedExceptionMessage needs traversable options.
	 */
	public function testNonTraversableOptionsAreInvalid()
	{
		$obj = new Select('T_Field');
		$obj->setOptions("Non Traversable");
	}
	
	/**
	 * Unset data throws.
	 *
	 * @expectedException LogicException
	 */
	public function testUnsetData()
	{
		$object = new Select('T_Field');
		$object->get();
	}

	/**
	 * Unset Text Field throws.
	 *
	 * @expectedException LogicException
	 */
	public function testUnsetText()
	{
		$data = [
			['Text_Field' => 'One',
			 'ID_Field'   => 1],
			['Text_Field' => 'Two',
			 'ID_Field'   => 2]];
		
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
			['Text_Field' => 'One',
			 'ID_Field'   => 1],
			['Text_Field' => 'Two',
			 'ID_Field'   => 2]];
		
		$object = new Select('Text_Field', 'Value_Field');
		$object->setOptions($data);
		$object->get();
	}
}
// EOF