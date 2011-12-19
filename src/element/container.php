<?php


class Element_Container extends Element
{ 
   public function __construct(Array $setup)
   {
      $setup = array_merge(
	 array('Container_Attribs' => array('class' => 'Container'),
	       'Container_Tag'     => 'div',
	       'Items'             => NULL,
	       'Parent_Attribs'    => array(),
	       'Parent_Tag'        => 'div'),
	 $setup);

      if (!isset($setup['Items']))
      {
	 throw new InvalidArgumentException(__METHOD__ . ' needs Items');
      }
      
      $children = array();

      foreach ($setup['Items'] as $item)
      {
	 $children[] = array($setup['Container_Tag'],
			     $setup['Container_Attribs'],
			     array('Children' => $item));
      }
      
      parent::__construct(array($setup['Parent_Tag'],
				$setup['Parent_Attribs'],
				array('Children' => $children)));
   }
}

// EOF