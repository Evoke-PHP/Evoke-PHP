<?php
declare(strict_types = 1);
/**
 * Abstract Data Decorator
 *
 * @package Model
 */
namespace Evoke\Model\Data;

/**
 * Abstract Data Decorator
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Model
 */
abstract class Decorator implements FlatIface
{
    /**
     * Data
     * @var FlatIface
     */
    protected $data;

    /**
     * Construct a Decorator object for Data.
     *
     * @param FlatIface $data
     */
    public function __construct(FlatIface $data)
    {
        $this->data = $data;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Return the number of records in the data.
     *
     * @return int
     */
    public function count() : int
    {
        return $this->data->count();
    }

    /**
     * Return the current record of data.
     *
     * @return Decorator
     */
    public function current() : Decorator
    {
        return $this->data->current();
    }

    /**
     * Get the current record as a simple array (without iterator or class properties).
     *
     * @return mixed[] The record that we are managing.
     */
    public function getRecord() : array
    {
        return $this->data->getRecord();
    }

    /**
     * Whether the data is empty or not.
     *
     * @return bool Whether the data is empty or not.
     */
    public function isEmpty() : bool
    {
        return $this->data->isEmpty();
    }


    /**
     * Return the key of the current data item.
     *
     * @return string
     */
    public function key() : string
    {
        return $this->data->key();
    }

    /**
     * Get the next record of data. Set the next record within the Data object and return the object.
     *
     * @return Decorator|bool Return the next data object, or boolean false.
     */
    public function next()
    {
        return $this->data->next();
    }

    /**
     * Provide the array isset operator.
     *
     * @param mixed $offset The offset to check for existence.
     * @return bool Whether the offset exists.
     *
     */
    public function offsetExists($offset)
    {
        return $this->data->offsetExists($offset);
    }

    /**
     * Provide the array access operator.
     *
     * @param mixed $offset The offset to get.
     * @return mixed The value at the offset.
     *
     */
    public function offsetGet($offset)
    {
        return $this->data->offsetGet($offset);
    }

    /**
     * Set the value at the offset.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->data->offsetSet($offset, $value);
    }

    /**
     * Unset the value at the offset
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->data->offsetUnset($offset);
    }

    /**
     * Rewind to the first record of data.
     */
    public function rewind()
    {
        $this->data->rewind();
    }

    /**
     * Set the data that we are managing.
     *
     * @param mixed[] $data The value for the data.
     */
    public function setData(Array $data)
    {
        $this->data->setData($data);
    }

    /**
     * Whether there are still data records to iterate over.
     *
     * @return bool Whether the current data record is valid.
     */
    public function valid() : bool
    {
        return $this->data->valid();
    }
}
// EOF
