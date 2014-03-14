<?php
/**
 * TreeBuilder
 *
 * @package Model\Data
 */
namespace Evoke\Model\Data;

/**
 * TreeBuilder
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2013 Paul Young
 * @license   MIT
 * @package   Model\Data
 */
class TreeBuilder
{
	/**
	 * The left field name.
	 * @var string
	 */
	protected $left;

	/**
	 * The right field name.
	 * @var string
	 */
	protected $right;

	/**
	 * Construct a TreeBuilder object.
	 *
	 * @param string Left.
	 * @param string Right.
	 */
	public function __construct(/* string */ $left  = 'Left',
	                            /* string */ $right = 'Right')
	{
		$this->left  = $left;
		$this->right = $right;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Build a tree from Modified Preorder Tree Traversal data.
	 *
	 * @param mixed[] The Modified Preorder Tree Traversal data.
	 * @return Tree The tree.
	 */
	public function build(Array $mptt)
	{
		$rootNode = new Tree;
		$rootNode->set($mptt[0]);
		$level = 0;
		$treePtrs = array();
		$children = array();
		$children[$level] =
			($mptt[0][$this->right] - $mptt[0][$this->left] - 1) / 2;
		$treePtrs[$level++] =& $rootNode;
		
		for ($i = 1; $i < count($mptt); ++$i)
		{
			$node = new Tree;
			$node->set($mptt[$i]);
			$treePtrs[$level - 1]->add($node);
			
			// We have processed the node, update the child counts.
			for ($lev = $level - 1; $lev >= 0; --$lev)
			{
				--$children[$lev];
			}
			
			while ($level > 0 && $children[$level - 1] === 0)
			{
				unset($children[--$level]);
			}
			
			// If we have children update the tree pointers and level.
			if ($mptt[$i][$this->right] - $mptt[$i][$this->left] > 1)
			{
				$children[$level] = (($mptt[$i][$this->right] -
				                      $mptt[$i][$this->left] - 1) / 2);
				$treePtrs[$level++] =& $node;
			}
			
			// Unset the local reference to node so that we can use it as a
			// variable again.
			unset($node);
		}

		return $rootNode;
	}
}
// EOF