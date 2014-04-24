<?php
/**
 * DB Data Builder
 *
 * @package Model\Data
 */
namespace Evoke\Model\Data;

use DomainException,
    Evoke\Model\Data\Metadata\DB;

/**
 * DB Data Builder
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
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

    /**
     * Separator
     * @var string
     */
    protected $separator;

    /**
     * Construct a DBDataBuilder object.
     *
     * @param string Separator between the Child Table and Field in a join.
     */
    public function __construct(/* string */ $separator = '_T_')
    {
        $this->separator = $separator;
    }
    
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Build hierarchical DB Data containers and the associated metadata
     * structure from flat metadata. All table names should be as per the
     * possibly aliased result data that will be used with the object.
	 *
	 * @param mixed[] $fields      Array of table names to fields.
	 * @param mixed[] $joins
     * Array of table names to join specifcations of the form:
	 *     <Parent_Field>=<Child_Table><Separator><Child_Field>
	 * @param mixed[] $primaryKeys Array of Table Aliases to primary keys.
	 * @param string  $tableName   Name of the table.
	 */
	public function build(Array        $fields,
                          Array        $joins,
	                      Array        $primaryKeys,
	                      /* string */ $tableName)
	{
		if (empty($this->metadataCache))
		{
			$this->fillMetadataCache($fields, $joins, $primaryKeys, $tableName);
		}

		return $this->buildData($fields, $joins, $primaryKeys, $tableName);
	}

	/*********************/
	/* Protected Methods */
	/*********************/

	/**
	 * Build the data using the metadata cache.
	 *
	 * @param mixed[] $fields      Array of table names to fields.
	 * @param mixed[] $joins
     * Array of table names to join specifcations of the form:
	 *     <Parent_Field>=<Child_Table><Separator><Child_Field>
	 * @param mixed[] $primaryKeys Array of Table Aliases to primary keys.
	 * @param string  $tableName   Name of the table.
	 */
	protected function buildData(Array $fields,
	                             Array $joins,
	                             Array $primaryKeys,
	                             /* String */ $tableName)
	{
		$dataJoins = array();

		if (isset($joins[$tableName]))
		{
			foreach ($joins[$tableName] as $join)
			{
                $dataJoins[$join] = $this->buildData(
                    $fields, $joins, $primaryKeys, $this->getChildTable($join));
			}
		}	

		return new Data($this->metadataCache[$tableName], $dataJoins);
	}

	/**
	 * Fill the metadata cache following all of the joins in the flat metadata.
	 *
	 * @param mixed[] $fields      Array of table names to fields.
	 * @param mixed[] $joins
     * Array of table names to join specifcations of the form:
	 *     <Parent_Field>=<Child_Table><Separator><Child_Field>
	 * @param mixed[] $primaryKeys Array of Table Aliases to primary keys.
	 * @param string  $tableName   Name of the table.
	 */
	protected function fillMetadataCache(Array $fields,
	                                     Array $joins,
	                                     Array $primaryKeys,
	                                     /* String */ $tableName)
	{
		$metadataJoins = array();
		
		if (!empty($joins[$tableName]))
		{
			foreach ($joins[$tableName] as $join)
			{
                $joinTable = $this->getChildTable($join);
                
				if (!isset($this->metadataCache[$joinTable]))
				{
					$this->fillMetadataCache(
						$fields, $joins, $primaryKeys, $joinTable);
				}

				$metadataJoins[$join] = $this->metadataCache[$joinTable];
			}
		}

		$this->metadataCache[$tableName] = new DB(
            empty($fields[$tableName]) ? array() : $fields[$tableName],
            $metadataJoins,
            empty($primaryKeys[$tableName]) ?
            array() : $primaryKeys[$tableName],
            $tableName);
	}

    /**
     * Get the child table from the join which is between = and the separator.
     *
     * @param string The join to get the child table from.
     * @return string The child table of the join.
     * @throws DomainException If the join does not contain a child table.
     */
    protected function getChildTable($join)
    {
        if (preg_match('~=(.*)' . preg_quote($this->separator) . '~',
                       $join,
                       $match))
        {
            return $match[1];
        }

        throw new DomainException('Missing child table in join: ' . $join);
    }   
}
// EOF
