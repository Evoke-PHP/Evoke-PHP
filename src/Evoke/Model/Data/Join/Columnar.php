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
    protected $fields;

    /**
     * Joint Key
     *
     * @var string
     */
    protected $jointKey;

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
        Array $keys = ['ID'],
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

                $data[$key]                  = $columnData;
                $data[$key][$this->jointKey] = [];
            }

            foreach ($this->joins as $joinID => $join) {
                $jointData = $join->arrangeFlatData([$result]);

                if (!empty($jointData)) {
                    if (!isset($data[$key][$this->jointKey][$joinID])) {
                        $data[$key][$this->jointKey][$joinID] = $jointData;
                    } else {
                        $data[$key][$this->jointKey][$joinID] += $jointData;
                    }
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
