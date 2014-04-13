<?php
/**
 * DB Metadata
 *
 * @package Model\Data\Metadata
 */
namespace Evoke\Model\Data\Metadata;

use DomainException,
	InvalidArgumentException;

/**
 * <h1>DB Metadata</h1>
 *
 * Describe the data for a database.  Relational databases contain special
 * information that describes the data stored in them:
 *
 * - Primary keys are used to uniquely identify records.
 * - Foreign keys are used to join information between tables.
 *
 * This information is almost enough to be able to deal with the data in a
 * meaningful way.  The only remaining this is to:
 *
 * - Ensure that the data retrieved retains the table information.
 *
 * Using a hierarchical metadata structure we can map a flat set of results from
 * the database to a meaningful hierarchical structure.
 *
 * Usage
 * =====
 *
 * Generally metadata should be used via \ref Evoke\Model\Data\Data.  It is a
 * complex task to build a metadata structure and a data container so
 * \ref Evoke\Model\Data\DBDataBuilder can be used to build the multiple tree
 * structures required to muster and represent hierarchical data.
 *
 * Below is an example structure of the DB metadata which aims to provide a list
 * of products, each with a set of related images and a size list (which for
 * simplicity we are not dereferencing to obtain the individual sizes).
 *
 * Database Structure
 * ------------------
 *
 * (PK = Primary Key, FK = Foreign Key):
 *
 * <pre>
 *   +=================+
 *   | Product         |     +==============+
 *   +-----------------+     | Image_List   |
 *   | PK | ID         |     +--------------+     +===========+
 *   |    | Name       |     | PK | ID      |     | Image     |
 *   | FK | Image_List |---->|    | List_ID |     +-----------+
 *   | FK | Size_List  |-.   | FK | Image   |---->| PK | ID   |
 *   +=================+ |   +==============+     |    | Name |
 *                       |                        +===========+
 *                       |   +==============+
 *                       |   | Size_List    |
 *                       |   +--------------+
 *                       |   | PK | ID      |
 *                       `-->|    | List_ID |
 *                           +==============+
 * </pre>
 *
 * SQL Statement
 * -------------
 *
 * The following SQL statement would be used to get the flat results:
 *
 * <pre><code>
   SELECT
	   Product.ID        AS Product_T_ID,
	   Product.Name      AS Product_T_Name,
	   Image_List.ID     AS Image_List_T_ID,
	   Image.ID          AS Image_T_ID,
	   Image.Name        AS Image_T_Name,
	   Size_List.ID      AS Size_List_T_ID,
	   Size_List.Size_ID AS Size_List_T_Size_ID
   FROM
	   Product
	   LEFT JOIN Image_List ON Product.Image_List = Image_List.List_ID
	   LEFT JOIN Image      ON Image_List.Image   = Image.ID
	   LEFT JOIN Size_List  ON Product.Size_List  = Size_List.List_ID
   </code></pre>
 *
 * Metadata Structure
 * ------------------
 *
 * The Metadata starting from the leaves would be:
 *
 * <pre><code>
   // Order of parameters: Fields, Joins, Primary Keys, Table Name, Table Alias
   $imageMetadata = new DB(
	   ['Name'],
	   [],
	   ['ID'],
	   'Image');
   $imageListMetadata = new DB(
	   [],
	   ['Image' => $imageMetadata],
	   ['ID'],
	   'Image_List');
   $sizeListMetadata = new DB(
	   ['List_ID'],
	   [],
	   ['ID'],
	   'Size_List');
   $productMetadata = new DB(
	   ['Name'],
	   ['Image_List' => $imageListMetadata,
		'Size_List'  => $sizeListMetadata],
	   ['ID'],
	   'Product');
   </code></pre>
 *
 * Example Flat Input Data
 * -----------------------
 *
 * <pre><code>
	[['Product_T_ID'        => 1,
	  'Product_T_Name'      => 'P_One',
	  'Image_List_T_ID'     => NULL,
	  'Image_T_ID'          => NULL,
	  'Image_T_Name'        => NULL,
	  'Size_List_T_ID'      => NULL,
	  'Size_List_T_Size_ID' => NULL],
	 ['Product_T_ID'        => 2,
	  'Product_T_Name'      => 'P_Two',
	  'Image_List_T_ID'     => 1,
	  'Image_T_ID'          => 1,
	  'Image_T_Name'        => 'Image.png',
	  'Size_List_T_ID'      => NULL,
	  'Size_List_T_Size_ID' => NULL],
	 ['Product_T_ID'        => 3,
	  'Product_T_Name'      => 'P_Three',
	  'Image_List_T_ID'     => 2,
	  'Image_T_ID'          => 2,
	  'Image_T_Name'        => 'I_One.png',
	  'Size_List_T_ID'      => 1,
	  'Size_List_T_Size_ID' => '1'],
	 ['Product_T_ID'        => 3,
	  'Product_T_Name'      => 'P_Three',
	  'Image_List_T_ID'     => 2,
	  'Image_T_ID'          => 3,
	  'Image_T_Name'        => 'I_Two.png',
	  'Size_List_T_ID'      => 1,
	  'Size_List_T_Size_ID' => '1']];
 * </code></pre>
 *
 * Arranged Data
 * -------------
 *
 * <pre><code>
	 [1 => ['ID'         => 1,
			'Name'       => 'P_One',
			'Joint_Data' =>
			['Image_List' => [],
			 'Size_List'  => []]],
	  2 => ['ID'         => 2,
			'Name'       => 'P_Two',
			'Joint_Data' =>
			['Image_List' => [
				 1 => ['ID'         => 1,
					   'Joint_Data' =>
					   ['Image' => [
							   1 => ['ID'   => 1,
									 'Name' => 'Image.png']]]]],
			 'Size_List'  => []]],
	  3 => ['ID'         => 3,
			'Name'       => 'P_Three',
			'Joint_Data' =>
			['Image_List' => [
				 2 => ['ID'         => 2,
					   'Joint_Data' =>
					   ['Image' => [
							   2 => ['ID'   => 2,
									 'Name' => 'I_One.png'],
							   3 => ['ID'   => 3,
									 'Name' => 'I_Two.png']]]]],
			 'Size_List'  => [
				 1 => ['ID'      => 1,
					   'Size_ID' => '1']]]]];
   </code></pre>
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   Model\Data\Metadata
 */
class DB implements MetadataIface
{
	/**
	 * Fields for the database table.
	 * @var string[]
	 */
	protected $fields;

	/**
	 * Joins from the database table.
	 * <pre><code>
	 * // The joins can be supplied as uniquely as they need to be referred to.
	 * [<Parent_Field>                                       => $metadata,
	 *  <Parent_Field>=<Child_Table><Separator><Child_Field> => $metadata]
	 * </code></pre>
	 *
	 * @var mixed[]
	 */
	protected $joins;

	/**
	 * Field to use for joining data.
	 * @var string
	 */
	protected $jointKey;

	/**
	 * Primary keys for the database table.
	 * @var string[]
	 */
	protected $primaryKeys;

	/**
	 * Separator between child table and fields.
	 * @var string
	 */
	protected $separator;

	/**
	 * TableAlias
	 * @var string
	 */
	protected $tableAlias;

	/**
	 * TableName
	 * @var string
	 */
	protected $tableName;

	/**
	 * Construct the metadata that describes the database data.
	 *
	 * @param string[]    Fields for the database table.
	 * @param mixed[]     Joins from the database table.
	 * @param string[]    Primary keys for the database table.
	 * @param string      Table Name.
	 * @param string|null Table Alias.
	 * @param string      Field to use for joining data.
	 * @param string      Separator between child table and fields.
	 *
	 * @throws InvalidArgumentException If there are no primary keys.
	 */
	public function __construct(Array        $fields,
								Array        $joins,
								Array        $primaryKeys,
								/* string */ $tableName,
								/* string */ $tableAlias = NULL,
								/* string */ $jointKey   = 'Joint_Data',
								/* string */ $separator  = '_T_')
	{
		if (empty($primaryKeys))
		{
			throw new InvalidArgumentException('Primary Keys cannot be empty.');
		}

		$this->fields      = $fields;
		$this->joins       = $joins;
		$this->jointKey    = $jointKey;
		$this->primaryKeys = $primaryKeys;
		$this->separator   = $separator;
		$this->tableAlias  = $tableAlias ?: $tableName;
		$this->tableName   = $tableName;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Arrange a set of results for the database according to the Join tree.
	 *
	 * @param mixed[] The flat result data.
	 * @param mixed[] The data already processed from the results.
	 *
	 * @returns mixed[] The data arranged into a hierarchy by the joins.
	 */
	public function arrangeFlatData(Array $results, Array $data=array())
	{
		$splitResults = array();

		foreach ($results as $result)
		{
			$splitResults[] = $this->splitResultByTables($result);
		}

		return $this->arrangeSplitResults($splitResults);
	}

	/**
	 * Arrange the results which have already been split into tables into
	 * hierarchical results according to the metadata.
	 *
	 * @param string[][][] The split results.
	 * @param mixed[] Any hierarchical data that has already been arranged.
	 * @return mixed[] The hierarchical results.
	 */
	public function arrangeSplitResults(Array $splitResults,
										Array $data = array())
	{
		foreach ($splitResults as $splitResult)
		{
			if (!empty($splitResult[$this->tableAlias]) &&
				$this->isResult($splitResult[$this->tableAlias]))
			{
				$rowID = $this->getRowID($splitResult[$this->tableAlias]);

				if (!isset($data[$rowID]))
				{
					$data[$rowID] = $splitResult[$this->tableAlias];
				}

				// If this result could contain information for referenced
				// tables lower in the heirachy set it in the joint data.
				if (!empty($this->joins))
				{
					if (!isset($data[$rowID][$this->jointKey]))
					{
						$data[$rowID][$this->jointKey] = array();
					}

					$jointData = &$data[$rowID][$this->jointKey];

					// Fill in the data for the joins by recursion.
					foreach($this->joins as $joinID => $metadata)
					{
						if (!isset($jointData[$joinID]))
						{
							$jointData[$joinID] = array();
						}

						// Recurse - Arrange the single result (splitResult).
						$jointData[$joinID] = $metadata->arrangeSplitResults(
							array($splitResult), $jointData[$joinID]);
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Get the join ID for the specified join or throw an exception if it can't
	 * be found uniquely.
	 *
	 * The join can be matched in a number of ways.
	 *
	 * - An exact join including the child table and field:
	 *     `Parent_Field=Child_Table<Separator>Child_Field`
	 * - An exact match of the parent field (if it is not ambiguous):
	 *     `Parent_Field`
	 * - A lowerCamelCase match (if it is not ambiguous):
	 *     `parentField`
	 *
	 * @param string Join to get the ID for.
	 * @return string The full uniquely matched join ID.
	 * @throws DomainException If the join cannot be found uniquely.
	 */
	public function getJoinID($join)
	{
		if (isset($this->joins[$join]))
		{
			return $join;
		}

		foreach (array_keys($this->joins) as $joinID)
		{
			$parentFieldLength = strpos($joinID, '=') ?: strlen($joinID);
			$parentField = substr($joinID, 0, $parentFieldLength);

			// If the parent field matches, or it matches as lowerCamelCase.
			if ($join === $parentField ||
				$join === lcfirst(str_replace('_', '', $parentField)))
			{
				if (isset($match))
				{
					throw new DomainException('Ambiguous match.');
				}

				$match = $joinID;
			}
		}

		if (isset($match))
		{
			return $match;
		}

		throw new DomainException('Join not found');
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	/**
	 * Get the row identifier for the curent row.
	 *
	 * @param mixed[] The data row.
	 * @throws DomainException If the row does not contain all primary keys.
	 */
	protected function getRowID(Array $data)
	{
		$rowID = '';

		foreach ($this->primaryKeys as $key)
		{
			if (!isset($data[$key]))
			{
				throw new DomainException(
					'Missing Primary Key: ' . $key . ' for table: ' .
					$this->tableAlias);
			}

			$rowID .= (empty($rowID) ? '' : '_') . $data[$key];
		}

		return $rowID;
	}

	/**
	 * Split a result by the tables that the result data is from.  This can be
	 * done thanks to the separator that identifies each table.
	 *
	 * @param mixed[] A flat result that is to be split.
	 */
	protected function splitResultByTables(Array $result)
	{
		$splitResult = array();

		foreach ($result as $field => $value)
		{
			$separated = explode($this->separator, $field);

			if (count($separated) !== 2)
			{
				throw new DomainException(
					'Each flat result field should be able to be split by ' .
					'containing a single table separator.' . "\n" .
					'Flat result field: ' . var_export($field, true) . "\n" .
					'Table separator: ' . var_export($this->separator, true));
			}

			if (!isset($splitResult[$separated[0]]))
			{
				$splitResult[$separated[0]] = array();
			}

			$splitResult[$separated[0]][$separated[1]] = $value;
		}

		return $splitResult;
	}

	/*******************/
	/* Private Methods */
	/*******************/

	/**
	 * Determine whether the result is a result (has data for this table).
	 *
	 * @param mixed[] The result data.
	 *
	 * @return bool Whether the result contains information for this table.
	 */
	private function isResult(Array $result)
	{
		// A non result may be an array with all NULL entries, so we cannot just
		// check that the result array is empty. The easiest way is just to
		// check that there is at least one value that is set.
		foreach ($result as $resultData)
		{
			if (isset($resultData))
			{
				return true;
			}
		}

		return false;
	}
}
// EOF