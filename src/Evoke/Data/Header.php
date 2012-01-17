<?php
namespace Evoke;

class Data_Header extends Data
{
   public function __construct(Array $setup=array())
   {
      $setup += array('Left_Field'  => 'Lft',
		      'Right_Field' => 'Rgt');
      
      parent::__construct($setup);
   }
   
   /******************/
   /* Public Methods */
   /******************/

   /// Arrange the data.
   public function arrangeData()
   {
      $newData = array('Menu' => array());
      
      foreach ($this->data['Menu'] as $record)
      {
	 $newData['Menu'][$record['Name']] = $this->arrangeRecord($record);
      }

      return $newData;
   }
   
   /** Arrange a single record (which is a single tree) from the data.  We are
    *  assuming that the data is arranged by Left order so that we can build
    *  from the ROOT item, left to right.
    */
   public function arrangeRecord($record)
   {
      $list = array_values($record[$this->setup['Joint_Key']]['List_ID']);
      $tree = array();
      
      // We are adding from left to right, so all we need to remember is how
      // many levels deep we should be.
      $levelsDeep = 0;

      // Remember how many children we need to process before going back up
      // levels. The last entry in the array is the amount of children at the
      // current level, second to last = previous level, etc.
      $childrenToProcess = array();
      
      // Add each item from the list in its correct place.
      for ($i = 0; $i < count($list); ++$i)
      {
	 $item = $list[$i];
	 $numChildren = ($item[$this->setup['Right_Field']] -
			 $item[$this->setup['Left_Field']] - 1) / 2;

	 if ($numChildren > 0)
	 {
	    $item['Children'] = array();
	 }
	 
	 // Go to the correct depth of the tree and add the item.
	 $ref =& $tree;
	 
	 for ($depth = 0; $depth < $levelsDeep; $depth++)
	 {
	    $ref =& $ref[count($ref) - 1]['Children'];
	 }

	 $ref[] = $item;

	 // We have processed an item.
	 foreach ($childrenToProcess as &$children)
	 {
	    if (--$children === 0)
	    {
	       $levelsDeep--;
	    }
	 }

	 // Make sure we go down to the level of our children for as long as
	 // they need.
	 if ($numChildren > 0)
	 {
	    $childrenToProcess[] = $numChildren;
	    $levelsDeep++;
	 }

      }

      return $tree[0];
   }
}
// EOF