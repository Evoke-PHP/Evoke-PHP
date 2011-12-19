<?php


/// Element_Tab_Panel
class Element_Tab_Panel extends Element
{ 
   public function __construct($tabEntries, $setup=array())
   {
      $setup = array_merge(
	 array('Active_Class'         => 'Active',
	       'Attribs'              => array('class' => 'Tab_Panel'),
	       'Clear_Attribs'        => array('class' => 'Clear'),
	       'Content_Attribs'      => array('class' => 'Content'),
	       'Content_List_Attribs' => array('class' => 'Content_List'),
	       'Heading_Attribs'      => array('class' => 'Tab'),
	       'Heading_List_Attribs' => array('class' => 'Heading_List'),
	       'Inactive_Class'       => 'Inactive'),
	 $setup);

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
	    $this->buildListItem($tabEntry['Heading'], $headingAttribs);
	 $contentElems[] =
	    $this->buildListItem($tabEntry['Content'], $contentAttribs);
      }

      parent::__construct(
	 array('div',
	       $setup['Attribs'],
	       array('Children' => array(
			array('ul',
			      $setup['Heading_List_Attribs'],
			      array('Children' => $headingElems)),
			array('div',
			      $setup['Clear_Attribs']),
			array('ul',
			      $setup['Content_List_Attribs'],
			      array('Children' => $contentElems))))));
   }

   /*******************/
   /* Private Methods */
   /*******************/

   private function buildListItem($entry, $attribs=array())
   {
      if (is_string($entry))
      {
	 return array('li', $attribs, array('Text' => $entry));
      }

      if (!is_array($entry))
      {
	 $entry = array($entry);
      }

      return array('li', $attribs, array('Children' => $entry));
   }

}

// EOF