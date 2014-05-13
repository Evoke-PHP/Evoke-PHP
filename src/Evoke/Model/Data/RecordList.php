<?php
/**
 * RecordList
 *
 * @package Model
 */
namespace Evoke\Model\Data;

/**
 * RecordList
 *
 * @author Paul Young <evoke@youngish.org>
 * @copyright Copyright (c) 2014 Paul Young
 * @license MIT
 * @package Model
 */
class RecordList extends Decorator implements RecordListIface
{
    /**
     * A list of the selected records within the record list.
     * @var mixed[]
     */
    protected $selectedRecords = [];

    /******************/
    /* Public Methods */
    /******************/

    /**
     * Reset the selection of the specified record in the data.
     *
     * @param mixed[] The record that should no longer be selected.
     */
    public function clearSelectedRecord(Array $record)
    {
        $key = array_search($record, $this->selectedRecords);

        if ($key !== false)
        {
            unset($this->selectedRecords[$key]);
        }
    }

    /**
     * Reset all of the records in the list so that they are not selected.
     */
    public function clearSelectedRecords()
    {
        $this->selectedRecords = [];
    }

    /**
     * Whether there is a selected record within the record list.
     *
     * @return bool Whether there is a selected record within the record list.
     */
    public function hasSelectedRecord()
    {
        return !empty($this->selectedRecords);
    }

    /**
     * Whether the current record is selected.
     *
     * @return bool Whether the current record is selected.
     */
    public function isSelectedRecord()
    {
        return in_array($this->data->getRecord(), $this->selectedRecords);
    }

    /**
     * Select a record within the record list data.
     *
     * @param mixed[] The record to match.
     */
    public function selectRecord(Array $record)
    {
        $this->selectedRecords[] = $record;
    }
}
// EOF
