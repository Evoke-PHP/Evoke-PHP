<?php
/**
 * Model Read-Only access to flat data.
 *
 * @package Model
 */
namespace Evoke\Model\Data;

use BadMethodCallException;

/**
 * Model Read-Only access to flat data.
 *
 * An iterator is supplied to traverse the records within the data.  Fields from
 * the current record of the data are accessed as per a standard Array.
 *
 * Usage
 * -----
 *
 * <pre><code>
 * $object = new Data;
 * $object->setData($data);
 *
 * // Traverse over each record in the data.
 * foreach ($object as $record)
 * {
 *    // Access the fields of each record as though it is an array.
 *    $x = $record['Field'];
 *
 *    // Deny setting of data.
 *    $record['Field'] = 'any'; // This would throw an exception.
 * }
 * </code></pre>
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Model
 *
 * @SuppressWarnings(PHPMD.TooManyMethods) - We need a lot.
 */
class Flat implements FlatIface
{
    /**
     * The data that is being modelled.
     *
     * @var mixed[]
     */
    protected $data = [];

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Get the current record as a simple array (without iterator or class
     * properties).
     *
     * @return mixed[] The current record as a simple array.
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
     * @param mixed[] $data The data we want to manage.
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
     * @return FlatIface
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
     * @return FlatIface|bool Return the next data object, or boolean false.
     */
    public function next()
    {
        $nextItem = next($this->data);

        if ($nextItem === false) {
            $this->setRecord([]);

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

        if ($first !== false) {
            $this->setRecord($first);
        } else {
            $this->setRecord([]);
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
     * @param mixed $offset The offset to check for existence
     * @return bool Whether the offset exists.
     *
     */
    public function offsetExists($offset)
    {
        $record = current($this->data);

        return isset($record[$offset]);
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
        $record = current($this->data);

        return $record[$offset];
    }

    /**
     * We are required to make these available to complete the interface,
     * but we don't want the element to change, so this should never be called.
     *
     * @param mixed $offset
     * @param mixed $value
     * @throws BadMethodCallException *** ALWAYS ***
     */
    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException(
            __METHOD__ . ' should never be called - data is only ' .
            'transferable it is not to be modified.  It was called with ' .
            'offset: ' . $offset . ' and value: ' . $value);
    }

    /**
     * We are required to make these available to complete the interface,
     * but we don't want the element to change, so this should never be called.
     *
     * @param mixed $offset
     * @throws BadMethodCallException *** ALWAYS ***
     */
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException(
            __METHOD__ . ' should never be called - data is only ' .
            'transferable it is not to be modified.  It was called with ' .
            'offset: ' . $offset);
    }

    /*********************/
    /* Protected Methods */
    /*********************/

    /**
     * Extra actions to be performed upon updating the current record within the
     * data.
     *
     * @param mixed[] $record The current record that we are setting.
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function setRecord(Array $record)
    {
        // By default nothing extra needs to be done.
    }
}
// EOF