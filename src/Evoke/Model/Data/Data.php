<?php
/**
 * Data
 *
 * @package Model\Data
 */
namespace Evoke\Model\Data;

use Evoke\Model\Data\Join\JoinIface,
    OutOfBoundsException;

/**
 * A general purpose data structure that provides access to hierarchical data.
 * The tree of data is accessed using array access for the information at the
 * current level of the tree.  Lower levels are accessed using class properties.
 *
 * ###Joins
 *
 * Joins provide a way of representing trees of data commonly found in
 * relational databases and many other real world situations.  They allow us to
 * work with a hierarchy of information considering each part in the hierarchy
 * as a separate data unit.
 *
 * A Data object is a tree of data combined with its join structure. At each
 * level throughout the tree the correct Data and join structure represent the
 * tree at that level.  This is a tree of descending trees for both the
 * join structure and the Data.  This structure allows any part of the data to
 * stand alone as a Data object.  The Data is valid at all levels of the tree.
 *
 * Use `Evoke\Model\Data\DataBuilder::build()` to build Data easily.
 *
 * ###Usage
 *
 * This is an example of using Data for products each with a set of images.
 * Further details can be seen by looking at `Evoke\Model\Data\Join\Tabular`.
 *
 * <pre><code>
 * $data = $dataBuilder->build(* See DataBuilder for parameters to pass *);
 * $data->setData(* Flat results that get organised by the join structure *);
 *
 * // Traverse over each record in the data.
 * foreach ($data as $product)
 * {
 *     // Access a field as though it is an array.
 *     $x = $product['Name'];
 *
 *     // Access joint data via class properties (with ->).  The joint data is
 *     // itself a data object. The name used after -> is determined by the join
 *     // structure.
 *     foreach ($product->image as $images)
 *     {
 *         foreach ($images as $image)
 *         {
 *             $y = $image['Name']; // Array Access
 *         }
 *     }
 * }
 * </code></pre>
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Model\Data
 */
class Data extends Flat
{
    /**
     * Properties for the data.
     *
     * @var Data[]    $jointData     Joins for the data.
     * @var string    $jointKey      Field used to join the data in a record.
     * @var JoinIface $joinStructure Structure of the data we are modelling.
     */
    protected $jointData, $jointKey, $joinStructure;

    /**
     * Construct a Data model.
     *
     * @param JoinIface $joinStructure Structure of the data we are modelling.
     * @param Data[]    $jointData     Joins for the data.
     * @param string    $jointKey      Field used to join the data in a record.
     */
    public function __construct(JoinIface    $joinStructure,
                                Array        $jointData = [],
                                /* String */ $jointKey  = 'Joint_Data')
    {
        $this->joinStructure = $joinStructure;
        $this->jointData     = $jointData;
        $this->jointKey      = $jointKey;
    }

    /**
     * Get access to the joint data as though it is a property of the object.
     *
     * @param string $join
     * Join name that identifies the joint data uniquely in the join structure.
     * @return Data The joint data.
     * @throws OutOfBoundsException
     * If there is no container for the join (The structure of the data does not
     * contain the information requested by the consumer).
     */
    public function __get($join)
    {
        $joinID = $this->joinStructure->getJoinID($join);

        if (isset($this->jointData[$joinID]))
        {
            return $this->jointData[$joinID];
        }

        throw new OutOfBoundsException('no data container for join: ' . $join);
    }

    /**
     * Set the data that we are managing.
     *
     * @param mixed[] $data The data we want to manage.
     */
    public function setData(Array $data)
    {
        $this->data = $this->joinStructure->arrangeFlatData($data);
        $this->rewind();
    }

    /*********************/
    /* Protected Methods */
    /*********************/

    /**
     * Set data that has already been arranged by the join structure.
     *
     * @param mixed[] $arrangedData The data that has already been arranged.
     */
    protected function setArrangedData(Array $arrangedData)
    {
        $this->data = $arrangedData;
        $this->rewind();
    }

    /**
     * Set all of the Joint Data from the current record into the data
     * containers supplied by the references given at construction.
     *
     * @param mixed[] $record The current record to set the joint data with.
     */
    protected function setRecord(Array $record)
    {
        foreach ($this->jointData as $joinID => $data)
        {
            if (isset($record[$this->jointKey][$joinID]))
            {
                $data->setArrangedData($record[$this->jointKey][$joinID]);
            }
            else
            {
                $data->setArrangedData([]);
            }
        }
    }
}
// EOF