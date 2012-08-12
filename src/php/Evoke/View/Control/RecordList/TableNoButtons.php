<?php
namespace Evoke\View\Control\RecordList;

/**
 * TableNoButtons
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class TableNoButtons extends RecordList
{
	/*********************/
	/* Protected Methods */
	/*********************/

	/**
	 * Build the element holding the buttons in a row (No Buttons).
	 *
	 * @param mixed   The key for the row.
	 * @param mixed[] The data for the row.
	 *
	 * @return mixed[] Array of elements that make up the buttons.
	 */    
	protected function buildRowButtons($row, $rowData)
	{
		return array('div', array('class' => 'No_Buttons'));
	}
}
// EOF