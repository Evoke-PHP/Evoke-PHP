<?php
/**
 * Data
 *
 * @package Model\Data
 */
namespace Evoke\Model\Data;

use Evoke\Model\Data\Metadata\MetadataIface,
	OutOfBoundsException;

/**
 * <h1>Data</h1>
 *
 * Provide access to joint data via class properties according to metadata.
 *
 * By understanding data (using metadata) we can access the hierarchy of the
 * data structure.  This hierarchy is accessed through the joins that are
 * provided at construction.
 *
 * ###Joins
 * 
 * Joins provide a way of representing trees of data commonly found in
 * relational databases and many other real world situations.  They allow us to
 * work with a hierarchy of information considering each part in the hierarchy
 * as a separate data unit.
 *
 * A Data object is a tree of data combined with its metadata. At each level
 * throughout the tree the correct Data and Metadata objects represent the
 * tree at that level.  This is a tree of descending trees for both the
 * metadata which is itself a tree of descending trees and the data.  If you
 * aren't scared of that then seek professional help.
 *
 * Use `Evoke\Model\Data\DBDataBuilder::build()` to build Data easily.
 *
 * ###Usage
 *
 * This is an exmaple of using Data for products each with a set of images.
 * Further details can be seen by looking at `Evoke\Model\Data\Metadata\DB`.
 *
 * <pre><code>
 * $data = $dbDataBuilder->build(* See DBDataBuilder for parameters to pass *);
 * $data->setData(* Flat results that get organised by data and metadata *);
 *
 * // Traverse over each record in the data.
 * foreach ($data as $product)
 * {
 *     // Access a field as though it is an array.
 *     $x = $product['Name'];
 *
 *     // Access joint data (with ->).  The joint data is itself a data object
 *     // The name used after -> is determined by the metadata object.  We are
 *     // assuming that we can use lowerCamelCase thanks to the metadata.
 *     foreach ($product->imageList as $imageList)
 *     {
 *         $y = $imageList['Image'];
 *         $image = $imageList->image;
 *     }
 * }
 * </code></pre>
 * 
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Model\Data
 */
class Data extends Flat
{
	/**
	 * Properties for the data.
	 *
	 * @var DataIface[]   $joins    Joins for the data.
	 * @var string        $jointKey Field used to join the data in a record.
	 * @var MetadataIface $metadata Description of the data we are modelling.
	 */
	protected $joins, $jointKey, $metadata;

	/**
	 * Construct a Data model.
	 *
	 * @param MetadataIface Description of the data we are modelling.
	 * @param DataIface[]   Joins for the data.
	 * @param string        Field used to join the data in a record.
	 */
	public function __construct(MetadataIface $metadata,
	                            Array         $joins    = array(),
	                            /* String */  $jointKey = 'Joint_Data')
	{
		$this->joins    = $joins;
		$this->jointKey = $jointKey;
		$this->metadata = $metadata;
	}

	/**
	 * Get access to the joint data as though it is a property of the object.
	 * 
	 * @param string Join name that identifies the joint data uniquely in the
	 *               metadata.
	 * @return DataIface The joint data.
	 */
	public function __get($join)
	{
		$joinID = $this->metadata->getJoinID($join);

		if (isset($this->joins[$joinID]))
		{
			return $this->joins[$joinID];
		}
		
		throw new OutOfBoundsException('no data container for join: ' . $join);
	}

	/**
	 * Set the data that we are managing.
	 *
	 * @param mixed[] The data we want to manage.
	 */
	public function setData(Array $data)
	{
		$this->data = $this->metadata->arrangeFlatData($data);
		$this->rewind();
	}   

	/*********************/
	/* Protected Methods */
	/*********************/

	/**
	 * Set all of the Joint Data from the current record into the data
	 * containers supplied by the references given at construction.
	 *
	 * @param mixed[] The current record to set the joint data with.
	 */
	protected function setRecord(Array $record)
	{
		foreach ($this->joins as $joinID => $data)
		{
			if (isset($record[$this->jointKey][$joinID]))
			{
				$data->setData($record[$this->jointKey][$joinID]);
			}
			else
			{
				$data->setData(array());
			}
		}
	}
}
// EOF