<?php
namespace Evoke\Element\Control;

class Tab_Panel extends \Evoke\Element\Base
{ 
	public function __construct($setup=array())
	{
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

	/** Set the Tab panel entries.
	 *  @param tabEntries \array The tab panel entries for the element.
	 */
	public function set(Array $tabEntries)
	{
		$headingElems = array();
		$contentElems = array(); 

		foreach ($tabEntries as $tabEntry)
		{
			$headingAttribs = $setup['Heading_Attribs'];
			$contentAttribs = $setup['Content_Attribs'];
			$selectedStatus = ' ' . $setup['Inactive_Class'];
	 
			if (isset($tabEntry['Active']) && $tabEntry['Active'] == true)
			{
				$selectedStatus = ' ' . $setup['Active_Class'];
			}

			$headingAttribs['class'] .= $selectedStatus;
			$contentAttribs['class'] .= $selectedStatus;

			$headingElems[] =
				array('li', $headingAttribs, $tabEntry['Heading']);
			$contentElems[] =
				array('li', $contentAttribs, $tabEntry['Content']);
		}

		return parent::set(
			array('div',
			      $setup['Attribs'],
			      array(array('ul',
			                  $setup['Heading_List_Attribs'],
			                  $headingElems),
			            array('div', $setup['Clear_Attribs']),
			            array('ul',
			                  $setup['Content_List_Attribs'],
			                  $contentElems))));
	}
}
// EOF