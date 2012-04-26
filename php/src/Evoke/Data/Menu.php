<?php
namespace Evoke\Data;

class Menu extends Base
{
	/** @property $left
	 *  Left field name @string
	 */
	protected $left;

	/** @property $right
	 *  Right field name @string
	 */
	protected $right;

	/** Construct the menu data.
	 *  @param left  @string Left field name.
	 *  @param right @string Right field name.
	 */
	public function __construct(/* String */ $left='Lft',
	                            /* String */ $right='Rgt')
	{
		$this->left  = $left;
		$this->right = $right;
	}

	/******************/
	/* Public Methods */
	/******************/

	/** Get the menu as a tree.
	 */
	public function getMenu()
	{
		$data = array();
      
		foreach ($this->list as $menuItems)
		{
			$data[] = $this->getTree($menuItems);
		}
      
		return $data;
	}
   
	/*********************/
	/* Protected Methods */
	/*********************/
   
	/** Arrange the menu items into a tree.  We assume that the data is arranged
	 *  by Left order so that we can build from the ROOT item, left to right.
	 *  @param menuItems @object Data object for the menu items.
	 *  @return @array Menu tree.
	 */
	protected function getTree($menuItems)
	{      
		$tree = array();
      
		// We are adding from left to right, so all we need to remember is how
		// many levels deep we should be.
		$levelsDeep = 0;

		// Remember how many children we need to process before going back up
		// levels. The last entry in the array is the amount of children at the
		// current level, second to last = previous level, etc.
		$childrenToProcess = array();


		/// \todo This needs to be fixed and tested.
		throw new Exception(__METHOD__ . ' needs implementation to be tested.');
		
		foreach ($menuItems as $item)
		{
			$numChildren = ($item[$this->right] - $item[$this->left] - 1) / 2;
	 
			// Go to the correct depth of the tree and add the item.
			$ref =& $tree;
	 
			for ($depth = 0; $depth < $levelsDeep; $depth++)
			{
				$ref =& $ref[count($ref) - 1]['Children'];
			}

			$ref[] = $item->getRecord();

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