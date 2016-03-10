<?php
/**
 * Columnar Join
 *
 * @package Model\Data\Join
 */
namespace Evoke\Model\Data\Join;

/**
 * <h1>Columnar Join</h1>
 *
 * Join data by column.
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Model\Data\Join
 */
class Columnar extends Join
{
    /**
     * Columns
     *
     * @var string[]
     */
    protected $columns;

    /**
     * Joint Key
     *
     * @var string
     */
    protected $jointKey;

    /**
     * Keys
     *
     * @var string[]
     */
    protected $keys;

    /**
     * Construct a Columnar join object.
     *
     * @param string[] $columns
     * @param array    $keys
     * @param string   $jointKey
     * @param bool     $useAlphaNumMatch
     */
    public function __construct(
        Array $columns,
        Array $keys = ['id'],
        $jointKey = 'Joint_Data',
        $useAlphaNumMatch = true
    ) {
        parent::__construct($useAlphaNumMatch);

        $this->columns  = array_flip($columns);
        $this->jointKey = $jointKey;
        $this->keys     = array_flip($keys);
    }

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Arrange a set of results according to the Join tree.
     *
     * @param mixed[] $results The flat result data.
     * @return mixed[] The data arranged into a hierarchy by the joins.
     */
    public function arrangeFlatData(Array $results)
    {
        $data = [];
        $resultsToJoin = [];

        foreach ($results as $result) {
            $key = implode('_', array_intersect_key($result, $this->keys));

            if ($key === '') {
                continue;
            }

            if (!isset($data[$key])) {
                $columnData = array_intersect_key($result, $this->columns);
                $hasData    = false;

                foreach ($columnData as $val) {
                    if (isset($val)) {
                        $hasData = true;
                        break;
                    }
                }

                if (!$hasData) {
                    continue;
                }

                $data[$key] = $columnData;

                if (empty($this->joins)) {
                    continue;
                }

                $data[$key][$this->jointKey] = [];
            }

            if (!isset($resultsToJoin[$key])) {
                $resultsToJoin[$key] = [];
            }

            foreach ($this->joins as $joinID => $join) {
                if (!isset($resultsToJoin[$key][$joinID])) {
                    $resultsToJoin[$key][$joinID] = [];
                }

                $resultsToJoin[$key][$joinID][] = $result;
            }
        }

        foreach ($resultsToJoin as $key => $joinResults) {
            foreach ($joinResults as $joinID => $resultsForJoining) {
                $data[$key][$this->jointKey][$joinID] = $this->joins[$joinID]->arrangeFlatData($resultsForJoining);

                if (empty($data[$key][$this->jointKey][$joinID])) {
                    unset($data[$key][$this->jointKey][$joinID]);
                }
            }

            if (empty($data[$key][$this->jointKey])) {
                unset($data[$key][$this->jointKey]);
            }
        }

        return $data;
    }
}
// EOF
