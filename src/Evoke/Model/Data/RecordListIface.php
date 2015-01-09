<?php
/**
 * Record List Data Interface
 *
 * @package Model\Data
 */
namespace Evoke\Model\Data;

/**
 * Record List Data Interface
 *
 * @author    Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2015 Paul Young
 * @license   MIT
 * @package   Model\Data
 */
interface RecordListIface extends FlatIface
{
    /**
     * Reset the selection of the specified record in the data.
     *
     * @param mixed[] $record The record that should no longer be selected.
     */
    public function clearSelectedRecord(Array $record);

    /**
     * Reset all of the records in the list so that they are not selected.
     */
    public function clearSelectedRecords();

    /**
     * Whether there is a selected record within the record list.
     *
     * @return bool Whether there is a selected record within the record list.
     */
    public function hasSelectedRecord();

    /**
     * Whether the current record is selected.
     *
     * @return bool Whether the current record is selected.
     */
    public function isSelectedRecord();

    /**
     * Select a record within the record list data.
     *
     * @param mixed[] $record The record to match.
     */
    public function selectRecord(Array $record);
}
// EOF
