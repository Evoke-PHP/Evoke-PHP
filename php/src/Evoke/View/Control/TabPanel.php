<?php
namespace Evoke\View\Control;

use Evoke\View\ViewIface;

/**
 * TabPanel
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class TabPanel implements ViewIface
{ 
	/**
	 * @todo Fix to the new view interface.
	 */
	public function __construct($setup=array())
	{
		/// @todo Fix to new View interface.
		throw new \RuntimeException('Fix to new view interface.');
		
		$setup += array(
			'Active_Class'         => 'Active',
			'Attribs'              => array('class' => 'Tab_Panel'),
			'Clear_Attribs'        => array('class' => 'Clear'),
			'Content_Attribs'      => array('class' => 'Content'),
			'Content_List_Attribs' => array('class' => 'Content_List'),
			'Heading_Attribs'      => array('class' => 'Tab'),
			'Heading_List_Attribs' => array('class' => 'Heading_List'),
			'Inactive_Class'       => 'Inactive');

		parent::__construct($setup);
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Set the Tab panel entries.
	 *
	 * @param mixed[] The tab panel entries for the view.
	 */
	public function get(Array $tabEntries = array())
	{
		$headingElems = array();
		$contentElems = array(); 

		foreach ($tabEntries as $tabEntry)
		{
			$headingAttribs = $headingAttribs;
			$contentAttribs = $contentAttribs;
			$selectedStatus = ' ' . $inactiveClass;
	 
			if (isset($tabEntry['Active']) && $tabEntry['Active'] == true)
			{
				$selectedStatus = ' ' . $activeClass;
			}

			$headingAttribs['class'] .= $selectedStatus;
			$contentAttribs['class'] .= $selectedStatus;

			$headingElems[] =
				array('li', $headingAttribs, $tabEntry['Heading']);
			$contentElems[] =
				array('li', $contentAttribs, $tabEntry['Content']);
		}

		return array('div',
		             $attribs,
		             array(array('ul',
		                         $headingListAttribs,
		                         $headingElems),
		                   array('div', $clearAttribs),
		                   array('ul',
		                         $contentListAttribs,
		                         $contentElems)));
	}
}
// EOF