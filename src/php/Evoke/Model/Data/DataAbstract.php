<?php
/**
 * Model Read-Only access to data.
 *
 * @package Model
 */
namespace Evoke\Model\Data;

use BadMethodCallException;

/**
 * Model Read-Only access to data.
 *
 * Extended classes may use the correct business logic that is appropriate for
 * model layer to perform.  An iterator is supplied to traverse the array of
 * records that make up the data.  Fields from the current record of the data
 * are accessed as per a standard Array.
 *
 * Usage:
 * <pre><code>
 * $obj = new Data(array(),
 *                 array('List_ID' => $dataObjectForList));
 * // Setting the data of the parent sets the data for the joint lists (and
 * // their joint lists etc.).
 * $obj->setData($data);
 *
 * // Traverse over each record in the data.
 * foreach ($obj as $key => $record)
 * {
 *    // Access a field as though it is an array.
 *    $x = $record['Field'];
 *
 *    // Access joint data (with ->).  The joint data is itself a data object.
 *    // The name used after -> is the lowerCamelCase (_ID is removed
 *    // automatically).
 *    foreach ($record->list as $listRecord)
 *    {
 *       $y = $listRecord['Joint_Record_Field'];
 *    }
 * }
 * </code></pre>
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 *
 * @SuppressWarnings(PHPMD.TooManyMethods) - We need a lot.
 */
abstract class DataAbstract implements DataIface
{
	/**
	 *  The data that is being modelled.
	 * @var mixed[]
	 */
	protected $data;

	/**
	 *  Construct a Data model.
	 *
	 *  @param mixed[] Raw data that we are modelling.
	 */
	public function __construct(Array $data = array())
	{
		$this->setData($data);
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the current record as a simple array (without iterator or class
	 * properties).
	 *
	 * @return mixed[] The record that we are managing.
	 */
	public function getRecord()
	{
		return current($this->data);
	}
	
	/**
	 * Whether the data is empty or not.
	 *
	 * @return bool Whether the data is empty or not.
	 */
	public function isEmpty()
	{
		return empty($this->data);
	}
   
	/**
	 * Set the data that we are managing.
	 *
	 * @param mixed[] The data we want to manage.
	 */
	public function setData(Array $data)
	{
		$this->data = $data;
		$this->rewind();
	}   
   
	/***********************/
	/* Implements Iterator */
	/***********************/

	/**
	 * Return the current record of data (as a Data object with iterator and
	 * reference access).  This is just the object as the object implements the
	 * iterator and references.
	 *
	 * @return Evoke\Model\Data\DataIface
	 */
	public function current()
	{
		return $this;
	}

	/**
	 * Return the key of the current data item.
	 *
	 * @return string|int
	 */
	public function key()
	{
		return key($this->data);
	}

	/**
	 * Get the next record of data. Set the next record within the Data object
	 * and return the object.
	 *
	 * @return Evoke\Model\Data\DataIface|bool Return the next data object, or
	 *                                         boolean false.
	 */
	public function next()
	{
		$nextItem = next($this->data);

		if ($nextItem === false)
		{
			$this->setRecord(array());
			return false;
		}

		$this->setRecord($nextItem);
		return $this;
	}

	/**
	 * Rewind to the first record of data.
	 */
	public function rewind()
	{
		$first = reset($this->data);

		if ($first !== false)
		{
			$this->setRecord($first);
		}
		else
		{
			$this->setRecord(array());
		}
	}

	/**
	 * Whether there are still data records to iterate over.
	 *
	 * @return bool Whether the current data record is valid.
	 */
	public function valid()
	{
		return (current($this->data) !== false);
	}

	/**************************/
	/* Implements ArrayAccess */
	/**************************/
   
	/**
	 * Provide the array isset operator.
	 *
	 * @param string The offest to check for existence.
	 *
	 * @return bool Whether the offset exists.
	 */
	public function offsetExists($offset)
	{
		$record = current($this->data);
		return isset($record[$offset]);
	}

	/**
	 * Provide the array access operator.
	 *
	 * @param string The offset to get.
	 *
	 * @return mixed The value at the offset.
	 */
	public function offsetGet($offset)
	{
		$record = current($this->data);
		return $record[$offset];
	}

	/**
	 * We are required to make these available to complete the interface,
	 * but we don't want the element to change, so this should never be called.
	 *
	 * @param mixed Offset.
	 * @param mixed Value.
	 *
	 * @throw RuntimeException *** ALWAYS ***
	 */
	public function offsetSet($offset, $value)
	{
		throw new BadMethodCallException(
			__METHOD__ . ' should never be called - data is only ' .
			'transferrable it is not to be modified.  It was called with ' .
			'offset: ' . $offset . ' and value: ' . $value);
	}

	/**
	 * We are required to make these available to complete the interface,
	 * but we don't want the element to change, so this should never be called.
	 *
	 * @param mixed Offset.
	 *
	 * @throw RuntimeException *** ALWAYS ***
	 */
	public function offsetUnset($offset)
	{
		throw new BadMethodCallException(
			__METHOD__ . ' should never be called - data is only ' .
			'transferrable it is not to be modified.  It was called with ' .
			'offset: ' . $offset);
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	/**
	 * Extra actions to be performed upon updating the current record within the
	 * data.
	 *
	 * @param mixed[] The current record that we are setting.
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	protected function setRecord(Array $record)
	{
		// By default nothing extra needs to be done.
	}
}
// EOF