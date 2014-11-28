<?php
/**
 * Tabular Join
 *
 * @package Model\Data\Join
 */
namespace Evoke\Model\Data\Join;

use DomainException;

/**
 * <h1>Tabular Join</h1>
 *
 * Join data by table.
 *
 * Using a hierarchical join structure we can map a flat set of results like we
 * might receive from a database query to a meaningful hierarchical structure.
 *
 * Usage
 * =====
 *
 * Generally joins should be used via \ref Evoke\Model\Data\Data.  It is a
 * complex task to build a join structure and the associated data containers so
 * \ref Evoke\Model\Data\DBDataBuilder can be used to build the multiple tree
 * structures required to muster and represent hierarchical data.
 *
 * Below is an example of using Tabular Joins to obtain a meaningful
 * hierarchical structure from a database. The example is for a list of products
 * which can contain a set of related images.
 *
 * Database Structure
 * ------------------
 *
 * (PK = Primary Key, FK = Foreign Key):
 *
 * <pre>
 *                          +=================+
 *   +============+         | Product_Images  |
 *   | Product    |         +-----------------+         +============+
 *   +------------+         | PK | ID         |         | Image      |
 *   | PK | ID    |-||----|<| FK | Product_ID |         +------------+
 *   |    | Name  |         | FK | Image_ID   |>|----||-| PK | ID    |
 *   +============+         +=================+         |    | Name  |
 *                                                      +============+
 * </pre>
 *
 * SQL Statement
 * -------------
 *
 * The following SQL query would be used to get the flat results:
 *
 * <pre><code>
 SELECT
 Product.ID   AS Product_T_ID,
 Product.Name AS Product_T_Name,
 Image.ID     AS Image_T_ID,
 Image.Name   AS Image_T_Name
 FROM
 Product
 LEFT JOIN Product_Images ON Product.ID = Product_Images.Product_ID
 LEFT JOIN Image          ON Image.ID   = Product_Images.Image_ID
 </code></pre>
 *
 * Note: The SQL query forces the results to a tabular format by prepending the
 *       result fields with the table name. This allows the tabular join to
 *       identify the tables which the result fields belong to.
 *
 * Join Structure
 * ------------------
 *
 * The Join structure that will help us arrange this data is:
 *
 * <pre><code>
 $joinStructure = new Tabular('Product');
 $joinStructure->addJoin('Image', new Tabular('Image'));
 </code></pre>
 *
 * Example Flat Input Data
 * -----------------------
 *
 * <pre><code>
 $results = [['Product_T_ID'   => 1,
 'Product_T_Name' => 'P_One',
 'Image_T_ID'     => NULL,
 'Image_T_Name'   => NULL],
 ['Product_T_ID'   => 2,
 'Product_T_Name' => 'P_Two',
 'Image_T_ID'     => 1,
 'Image_T_Name'   => 'Image.png'],
 ['Product_T_ID'   => 3,
 'Product_T_Name' => 'P_Three',
 'Image_T_ID'     => 2,
 'Image_T_Name'   => 'I_One.png'],
 ['Product_T_ID'   => 3,
 'Product_T_Name' => 'P_Three',
 'Image_T_ID'     => 3,
 'Image_T_Name'   => 'I_Two.png']];
 * </code></pre>
 *
 * Arrange the Data
 * ----------------
 *
 * <pre><code>
 $joinStructure->arrangeFlatData($results);
 </code></pre>
 *
 * Below is a pretty version of the hierarchical data from the arrangement:
 *
 * <pre><code>
 [1 => ['Name'       => 'P_One',
 'Joint_Data' =>
 ['Image' => []]],
 2 => ['Name'       => 'P_Two',
 'Joint_Data' =>
 ['Image' => [1 => ['Name' => 'Image.png']]]],
 3 => ['Name'       => 'P_Three',
 'Joint_Data' =>
 ['Image' => [2 => ['Name' => 'I_One.png'],
 3 => ['Name' => 'I_Two.png']]]]];
 </code></pre>
 *
 * The data has been arranged so that a list of products identified by their
 * primary keys contains their associated image lists correctly identified by
 * their image ID.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license   MIT
 * @package   Model\Data\Join
 */
class Tabular extends Join
{
    /**
     * Field to use for joining data in the arranged results.
     * @var string
     */
    protected $jointKey;

    /**
     * Keys used to identify records in the current table.
     * @var string[]
     */
    protected $keys;

    /**
     * Whether all flat result fields must be tabular (able to be identified
     * by their table prefix and separator before their field name).
     * @var bool
     */
    protected $requireAllTabularFields;

    /**
     * Separator between table and fields.
     * @var string
     */
    protected $separator;

    /**
     * Table name for the main records.
     * @var string
     */
    protected $tableName;

    /**
     * Construct the tabular join tree used to arrange the data.
     *
     * @param string   Table name for collecting the main results.
     * @param string[] Key fields for the records.
     * @param string   Field to use for joining data.
     * @param bool     Whether all result fields must be tabular.
     * @param string   Separator between table and fields.
     * @param bool     Whether we can refer to joins using a case-insenstive
     *                 alpha numeric match.
     */
    public function __construct(
        /* string */ $tableName,
        Array        $keys                    = ['ID'],
        /* string */ $jointKey                = 'Joint_Data',
        /* bool   */ $requireAllTabularFields = true,
        /* string */ $separator               = '_T_',
        /* bool   */ $useAlphaNumMatch        = true)
    {
        parent::__construct($useAlphaNumMatch);
        
        $this->jointKey                = $jointKey;
        $this->keys                    = $keys;
        $this->requireAllTabularFields = $requireAllTabularFields;
        $this->separator               = $separator;
        $this->tableName               = $tableName;
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Arrange a set of results for the database according to the Join tree.
     *
     * @param mixed[] The flat result data.
     * @return mixed[] The data arranged into a hierarchy by the joins.
     */
    public function arrangeFlatData(Array $results)
    {
        $splitResults = [];

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
                                        Array $data = [])
    {
        foreach ($splitResults as $splitResult)
        {
            if (!empty($splitResult[$this->tableName]) &&
                $this->isResult($splitResult[$this->tableName]))
            {
                $rowID  = $this->filterRowID($splitResult[$this->tableName]);
                $result = $this->filterRowFields(
                    $splitResult[$this->tableName]);

                if (!isset($rowID))
                {
                    // As we don't have a key to identify the row we must check
                    // to ensure that the result has not already been added.
                    $hasBeenAdded = false;

                    foreach ($data as $existingID => $existingEntry)
                    {
                        unset($existingEntry[$this->jointKey]);

                        if (!array_diff_assoc($existingEntry, $result))
                        {
                            $hasBeenAdded = true;
                            $rowID = $existingID;
                            break;
                        }
                    }

                    if (!$hasBeenAdded)
                    {
                        $data[] = $result;
                        end($data);
                        $rowID = key($data);
                    }
                }
                elseif (!isset($data[$rowID]))
                {
                    $data[$rowID] = $result;
                }

                // If this result could contain information for referenced
                // tables lower in the heirachy set it in the joint data.
                if (!empty($this->joins))
                {
                    if (!isset($data[$rowID][$this->jointKey]))
                    {
                        $data[$rowID][$this->jointKey] = [];
                    }

                    $jointData = &$data[$rowID][$this->jointKey];

                    // Fill in the data for the joins by recursion.
                    foreach($this->joins as $joinID => $join)
                    {
                        if (!isset($jointData[$joinID]))
                        {
                            $jointData[$joinID] = [];
                        }

                        // Recurse - Arrange the single result (splitResult).
                        $jointData[$joinID] = $join->arrangeSplitResults(
                            array($splitResult), $jointData[$joinID]);
                    }
                }
            }
        }

        return $data;
    }


    /*********************/
    /* Protected Methods */
    /*********************/

    /**
     * Get the row identifier for the curent row.
     *
     * @param mixed[] The data row.
     * @throws DomainException If the row does not contain all of the keys.
     */
    protected function filterRowID(Array $row)
    {
        $rowID = NULL;

        foreach ($this->keys as $key)
        {
            if (!isset($row[$key]))
            {
                throw new DomainException(
                    'Missing Key: ' . $key . ' for table: ' . $this->tableName);
            }

            $rowID .= (empty($rowID) ? '' : '_') . $row[$key];
        }

        return $rowID;
    }

    /**
     * Get the non-identifying fields from the current row.
     *
     * @param mixed[] The data row.
     */
    protected function filterRowFields(Array $row)
    {
        return array_diff_key($row, array_flip($this->keys));
    }

    /**
     * Split a result by the tables that the result data is from.  This can be
     * done thanks to the separator that identifies each table.
     *
     * @param mixed[] A flat result that is to be split.
     */
    protected function splitResultByTables(Array $result)
    {
        $splitResult = [];

        foreach ($result as $field => $value)
        {
            $separated = explode($this->separator, $field);

            if (count($separated) !== 2)
            {
                if (!$this->requireAllTabularFields)
                {
                    // Skip this non tabular field.
                    continue;
                }

                throw new DomainException(
                    'Each flat result field should be able to be split by ' .
                    'containing a single table separator.' . "\n" .
                    'Flat result field: ' . var_export($field, true) . "\n" .
                    'Table separator: ' . var_export($this->separator, true));
            }

            if (!isset($splitResult[$separated[0]]))
            {
                $splitResult[$separated[0]] = [];
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