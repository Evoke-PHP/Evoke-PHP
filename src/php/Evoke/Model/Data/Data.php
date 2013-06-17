<?php
namespace Evoke\Model\Data;

use Metadata\MetadataIface,
	OutOfBoundsException;

/**
 * Data
 * ====
 *
 * Provide access to joint data via class properties according to metadata.
 *
 * By understanding data (using metadata) we can access the hierarchy of the
 * data structure.  This hierarchy is accessed through the joins that are
 * provided at construction.
 *
 * Joins
 * -----
 *
 * Joins provide a way of representing trees of data commonly found in
 * relational databases and many other real world situations.  They allow us to
 * work with a hierarchy of information considering each part in the hierarchy
 * as a separate data unit.
 *
 * Example
 * -------
 *
 * Example from a relational database:
 *    List of products, each of a particular size with a set of related images.
 * 
 * SQL structure (PK = Primary Key, FK = Foreign Key):
 * <pre>
 *   +====================+     
 *   | Product            |     +===============+
 *   +--------------------+     | Image_List    |
 *   | PK | ID            |     +---------------+     +===============+
 *   |    | Name          |     | PK | ID       |     | Image         |
 *   | FK | Image_List_ID |---->|    | List_ID  |     +---------------+
 *   | FK | Size_ID       |-.   | FK | Image_ID |---->| PK | ID       |
 *   +====================+ |   +===============+     |    | Filename |
 *                          |                         +===============+
 *                          |   +===========+
 *                   	    |   | Size      |
 *                   	    |   +-----------+
 *                   	    `-->| PK | ID   |
 *                   	        |    | Name |
 *                   	        +===========+
 * </pre>
 *
 * SQL Syntax:
 *
 * <code><pre>
 * 'SELECT * FROM Product
 *  LEFT JOIN Image_List AS IL ON Product.Image_List_ID=IL.List_ID
 *  LEFT JOIN Image      AS I  ON IL.Image_ID=I.ID
 *  LEFT JOIN Size       AS S  ON Product.Size_ID=Size.ID;'
 * </pre></code>
 *
 * Product Joins:
 * <pre><code>
 * [
 *     'Image_List_ID=IL.List_ID' => $dataImageList,
 *     'Size_ID.ID=S.ID'          => $dataSize
 * ]
 * </code></pre>
 *
 * Image_List Joins:
 * <pre><code>
 * ['Image_ID=I.ID' => $dataImage]
 * </code></pre>
 *
 * The above is an abstract representation of the joins that would
 * represent the data.
 *
 * Usage
 * -----
 *
 * <pre><code>
 * $data = new Data($metadataProduct,
 *                  $joinsProduct);
 *
 * // Traverse over each record in the data.
 * foreach ($data as $product)
 * {
 *     // Access a field as though it is an array.
 *     $x = $product['Name'];
 *
 *     // Access joint data (with ->).  The joint data is itself a data object
 *     // The name used after -> is determined by the metadata object.  We are
 *     // assuming that we can use lowerCamelCase and that '_ID' is removed
 *     // automatically at the end.
 *     foreach ($product->imageList as $imageList)
 *     {
 *         $y = $imageList['Image_ID'];
 *         $image = $imageList->image;
 *     }
 * }
 * </code></pre>
 * 
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
class Data extends Flat
{
	protected
		/**
		 * Joins for the data.
		 * @var DataIface[]
		 */
		$joins,

		/**
		 * The field that is used to join the data in a record.
		 * @var string
		 */
		$jointKey,
		
		/**
		 * Description of the data we are modelling.
		 * @var MetadataIface
		 */
		$metadata;

	/**
	 * Construct a Data model.
	 *
	 * @param MetadataIface Description of the data we are modelling.
	 * @param DataIface[]   Joins for the data.
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