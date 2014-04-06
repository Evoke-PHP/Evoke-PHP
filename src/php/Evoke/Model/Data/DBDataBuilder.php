<?php
/**
 * DB Data Builder
 *
 * @package Model\Data
 */
namespace Evoke\Model\Data;

use Evoke\Model\Data\Metadata\DB,
	InvalidArgumentException;

/**
 * DB Data Builder
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2013 Paul Young
 * @license   MIT
 * @package   Model\Data
 */
class DBDataBuilder
{
	/**
	 * Metadata cache.
	 * @var DB[]
	 */
	protected $metadataCache = array();
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Build hierarchical DB Data containers and the associated metadata
	 * structure from flat metadata.
	 *
	 * @param mixed[]      Array of Table Aliases to fields.
	 * @param mixed[]      Array of Table Aliases to Join specifcations of the
	 *                     form:
	 * <pre><code>
	 *    ['Alias'  => 'a',
	 *     'Parent' => 'c',
	 *     'Table'  => 'd']
	 * </code></pre>
	 * @param mixed[]      Array of Table Aliases to primary keys.
	 * @param string       Name of the table.
	 * @param string|null  Alias of the table or NULL if the table name is to be
	 *                     used.
	 */
	public function build(Array $fields,
	                      Array $joins,
	                      Array $primaryKeys,
	                      /* String */ $tableName,
	                      /* String */ $tableAlias = NULL)
	{
		if (empty($this->metadataCache))
		{
			$this->fillMetadataCache(
				$fields, $joins, $primaryKeys, $tableName, $tableAlias);
		}

		return $this->buildData(
			$fields, $joins, $primaryKeys, $tableName, $tableAlias);
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	/**
	 * Build the data using the metadata cache.
	 *
	 * @param mixed[]      Array of Table Aliases to fields.
	 * @param mixed[]      Array of Table Aliases to Join specifcations of the
	 *                     form:
	 * <pre><code>
	 *    ['Alias'  => 'a',
	 *     'Parent' => 'c',
	 *     'Table'  => 'd']
	 * </code></pre>
	 * @param mixed[]      Array of Table Aliases to primary keys.
	 * @param string       Name of the table.
	 * @param string|null  Alias of the table or NULL if the table name is to be
	 *                     used.
	 */
	protected function buildData(Array $fields,
	                             Array $joins,
	                             Array $primaryKeys,
	                             /* String */ $tableName,
	                             /* String */ $tableAlias = NULL)
	{
		$dataJoins = array();
		$tableAlias = $tableAlias ?: $tableName;

		if (isset($joins[$tableAlias]))
		{
			foreach ($joins[$tableAlias] as $join)
			{
				// We are already sure that the join has a Parent and Table due
				// to the fillMetadataCache which runs before this method.
				$alias = isset($join['Alias']) ?
					$join['Alias'] : $join['Table'];

				$dataJoins[$join['Parent']] = $this->buildData(
					$fields, $joins, $primaryKeys, $joins['Table'], $alias);
			}
		}	

		return new Data($this->metadataCache[$tableAlias],
		                $dataJoins);
	}

	/**
	 * Fill the metadata cache following all of the joins in the flat metadata.
	 *
	 * @param mixed[]      Array of Table Aliases to fields.
	 * @param mixed[]      Array of Table Aliases to Join specifcations of the
	 *                     form:
	 * <pre><code>
	 *    ['Alias'  => 'a',
	 *     'Parent' => 'c',
	 *     'Table'  => 'd']
	 * </code></pre>
	 * @param mixed[]      Array of Table Aliases to primary keys.
	 * @param string       Name of the table.
	 * @param string|null  Alias of the table or NULL if the table name is to be
	 *                     used.
	 */
	protected function fillMetadataCache(Array $fields,
	                                     Array $joins,
	                                     Array $primaryKeys,
	                                     /* String */ $tableName,
	                                     /* String */ $tableAlias = NULL)
	{
		$tableAlias = $tableAlias ?: $tableName;
		$metadataFields = empty($fields[$tableAlias]) ?
			array() : $fields[$tableAlias];
		$metadataPrimaryKeys = empty($primaryKeys[$tableAlias]) ?
			array() : $primaryKeys[$tableAlias];
		$metadataJoins = array();
		
		if (!empty($joins[$tableAlias]))
		{
			foreach ($joins[$tableAlias] as $join)
			{
				if (!isset($join['Parent'], $join['Table']))
				{
					throw new InvalidArgumentException(
						'Joins must have Parent and Table.');
				}

				$alias = isset($join['Alias']) ?
					$join['Alias'] : $join['Table'];

				if (!isset($this->metadataCache[$alias]))
				{
					$this->fillMetadataCache(
						$fields, $joins, $primaryKeys, $join['Table'], $alias);
				}

				$metadataJoins[$join['Parent']] = $this->metadataCache[$alias];
			}
		}

		$this->metadataCache[$tableAlias] = new DB($metadataFields,
		                                           $metadataJoins,
		                                           $metadataPrimaryKeys,
		                                           $tableAlias,
		                                           $tableName);
	}
}
// EOF