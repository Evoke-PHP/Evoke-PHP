<?php
namespace Evoke\Data;

class Menu extends Base
{
	protected $left;
	protected $right;

	public function __construct(Array $setup=array())
	{
		$setup += array('Left_Field'  => 'Lft',
		                'Right_Field' => 'Rgt');

		parent::__construct($setup);

		$this->left = $setup['Left_Field'];
		$this->right = $setup['Right_Field'];
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Get the menu as a tree.
	 */
	public function getMenu()
	{
		$data = array();
      
		foreach ($this->List as $MenuItems)
		{
			$data[] = $this->getTree($MenuItems);
		}
      
		return $data;
	}
   
	/*********************/
	/* Protected Methods */
	/*********************/
   
	/** Arrange the menu items into a tree.  We assume that the data is arranged
	 *  by Left order so that we can build from the ROOT item, left to right.
	 *  @param MenuItems \Object Data object for the menu items.
	 *  \return \array Menu tree.
	 */
	protected function getTree($MenuItems)
	{      
		$tree = array();
      
		// We are adding from left to right, so all we need to remember is how
		// many levels deep we should be.
		$levelsDeep = 0;

		// Remember how many children we need to process before going back up
		// levels. The last entry in the array is the amount of children at the
		// current level, second to last = previous level, etc.
		$childrenToProcess = array();
      
		foreach ($MenuItems as $Item)
		{
			$numChildren = ($Item[$this->right] - $Item[$this->left] - 1) / 2;

			if ($numChildren > 0)
			{
				$menuItem['Children'] = array();
			}
	 
			// Go to the correct depth of the tree and add the item.
			$ref =& $tree;
	 
			for ($depth = 0; $depth < $levelsDeep; $depth++)
			{
				$ref =& $ref[count($ref) - 1]['Children'];
			}

			$ref[] = $Item->getRecord();

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