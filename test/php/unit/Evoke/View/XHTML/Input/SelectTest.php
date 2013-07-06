<?php
namespace Evoke_Test\View\XHTML\Input;

use Evoke\Model\Data\Flat,
	Evoke\View\XHTML\Input\Select,
	PHPUnit_Framework_TestCase;

class SelectTest extends PHPUnit_Framework_TestCase
{
	/*********/
	/* Tests */
	/*********/

	/**
	 * Create an object.
	 *
	 * @covers Evoke\View\XHTML\Input\Select::__construct
	 */
	public function testCreate()
	{
		$object = new Select('Text_Field');
		$this->assertInstanceOf('Evoke\View\XHTML\Input\Select', $object);
	}

	/**
	 * Get a select element.
	 *
	 * @covers Evoke\View\XHTML\Input\Select::get
	 * @covers Evoke\View\Data::setData
	 */
	public function testGetSelect()
	{
		// A mock would just be too confusing, with the myriad of expects.
		$rawData = [
			['Text_Field' => 'One',
			 'ID_Field'   => 1],
			['Text_Field' => 'Two',
			 'ID_Field'   => 2]];
		$data = new Flat;
		$data->setData($rawData);

		$object = new Select('Text_Field',
		                     'ID_Field',
		                     ['Attrib' => 'Main'],
		                     ['Attrib' => 'Option']);
		$object->setData($data);
		$this->assertSame(
			['select',
			 ['Attrib' => 'Main'],
			 [['option', ['Attrib' => 'Option', 'value' => 1], 'One'],
			  ['option', ['Attrib' => 'Option', 'value' => 2], 'Two']]],
			$object->get());
	}

	/**
	 * We can have an option selected.
	 *
	 * @covers Evoke\View\XHTML\Input\Select::get
	 * @covers Evoke\View\XHTML\Input\Select::setSelected
	 */
	public function testGetOptionSelected()
	{
		// A mock would just be too confusing, with the myriad of expects.
		$rawData = [
			['Text_Field' => 'One',
			 'ID_Field'   => 1],
			['Text_Field' => 'Two',
			 'ID_Field'   => 2]];
		$data = new Flat;
		$data->setData($rawData);

		$object = new Select('Text_Field',
		                     'ID_Field',
		                     ['Attrib' => 'Main'],
		                     ['Attrib' => 'Option']);
		$object->setSelected(2);
		$object->setData($data);
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
	 * Unset data throws.
	 *
	 * @covers            Evoke\View\XHTML\Input\Select::get
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
	 * @covers            Evoke\View\XHTML\Input\Select::get
	 * @expectedException LogicException
	 */
	public function testUnsetText()
	{
		// A mock would just be too confusing, with the myriad of expects.
		$rawData = [
			['Text_Field' => 'One',
			 'ID_Field'   => 1],
			['Text_Field' => 'Two',
			 'ID_Field'   => 2]];
		$data = new Flat;
		$data->setData($rawData);
		
		$object = new Select('T_Field');
		$object->setData($data);
		$object->get();
	}

	/**
	 * Unset Value Field throws.
	 *
	 * @covers            Evoke\View\XHTML\Input\Select::get
	 * @expectedException LogicException
	 */
	public function testUnsetValue()
	{
		// A mock would just be too confusing, with the myriad of expects.
		$rawData = [
			['Text_Field' => 'One',
			 'ID_Field'   => 1],
			['Text_Field' => 'Two',
			 'ID_Field'   => 2]];
		$data = new Flat;
		$data->setData($rawData);
		
		$object = new Select('Text_Field', 'Value_Field');
		$object->setData($data);
		$object->get();
	}
}
// EOF