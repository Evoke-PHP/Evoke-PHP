<?php
namespace Evoke\Model\Data;

/**
 * Menu
 *
 * Menu Model for MPTT data.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
class Menu extends Data implements MenuIface
{
	/** 
	 * Left field name
	 * @var string
	 */
	protected $left;

	/**
	 * Right field name
	 * @var string
	 */
	protected $right;

	/**
	 * Construct the menu data.
	 *
	 * @param string Left field name.
	 * @param string Right field name.
	 */
	public function __construct(Array        $data      = array(),
	                            Array        $dataJoins = array(),
	                            /* String */ $jointKey  = 'Joint_Data',
	                            /* String */ $left      = 'Lft',
	                            /* String */ $right     = 'Rgt')
	{
		parent::__construct($data, $dataJoins, $jointKey);
		
		$this->left  = $left;
		$this->right = $right;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the menu as a tree.
	 *
	 * @return mixed[] Menu tree.
	 */
	public function getMenu()
	{
		$data = array();
      
		foreach ($this as $menuItems)
		{
			$menuDetails = $menuItems->getRecord();
			$data[] = array('Name' => $menuDetails['Name'],
			                'Items' => $this->getTree($menuItems));
		}
      
		return $data;
	}
   
	/*********************/
	/* Protected Methods */
	/*********************/
   
	/**
	 * Arrange the menu items into a tree.  We assume that the data is arranged
	 * by Left order so that we can build from the ROOT item, left to right.
	 *
	 * @param mixed[] Data object for the menu items.
	 *
	 * @return mixed[] Menu tree.
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

		foreach ($menuItems->list as $item)
		{
			$numChildren = ($item[$this->right] - $item[$this->left] - 1) / 2;
	 
			// Go to the correct depth of the tree and add the item.
			$join =& $tree;
	 
			for ($depth = 0; $depth < $levelsDeep; $depth++)
			{
				$join =& $join[count($join) - 1]['Children'];
			}

			$join[] = array_merge(array('Children' => array()),
			                      $item->getRecord());

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

		return $tree;
	}
}
// EOF