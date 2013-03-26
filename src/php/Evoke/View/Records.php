<?php
/**
 * Records
 *
 * @package View
 */
namespace Evoke\View;

/**
 * Records
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Records implements ViewIface
{
	/**
	 * ViewRecord
	 * @var ViewIface
	 */
	protected $viewRecord;

	/**
	 * Construct a Records object.
	 *
	 * @param ViewIface ViewRecord.
	 */
	public function __construct(ViewIface $viewRecord)
	{
		$this->viewRecord = $viewRecord;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view of the records.
	 *
	 * @param mixed[] The parameters for the view (Record_List).
	 * @return mixed[] The view of the records.
	 */
	public function get(Array $params = array())
	{
		if (isset($params['Record_List']) ||
		    $params['Record_List'] instanceof RecordList)
		{
			throw new InvalidArgumentException('needs Record_List');
		}

		$entries = array();
		
		foreach ($params['Record_List'] as $record)
		{
			$entries = $this->viewRecord->get(
				array('Record' => $record));
		}

		return array('div',
		             array('class' => 'Record_List'),
		             $entries);
	}
}
// EOF