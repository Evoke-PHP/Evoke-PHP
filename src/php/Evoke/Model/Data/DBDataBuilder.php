<?php
/**
 * DB Data Builder
 *
 * @package Model\Data
 */
namespace Evoke\Model\Data;

use InvalidArgumentException;

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
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Build hierarchical DB metadata structure from flat metadata.
	 *
	 */
	public function build(Array $fields,
	                      Array $joins,
	                      Array $primaryKeys,
	                      /* String */ $tableName,
	                      /* String */ $tableAlias = NULL)
	{
		$metadataFields = array();
		$metadataJoins = array();
		$metadataPrimaryKeys = array();
		$tableAlias = $tableAlias ?: $tableName;
		
		if (!empty($fields[$tableAlias]))
		{
			$metadataFields = $fields[$tableAlias];
		}

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
				$metadataJoins[$join['Parent']] = $this->build(
					$fields, $joins, $primaryKeys, $join['Table'], $alias);
			}
		}

		if (!empty($primaryKeys[$tableAlias]))
		{
			$metadataPrimaryKeys = $primaryKeys[$tableAlias];
		}
		
		return new Metadata\DB($metadataFields,
		                       $metadataJoins,
		                       $metadataPrimaryKeys,
		                       $tableAlias,
		                       $tableName);
	}
	
}
// EOF