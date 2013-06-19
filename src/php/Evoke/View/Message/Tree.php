<?php
/**
 * Message Tree View
 *
 * @package View\Message
 */
namespace Evoke\View\Message;

use Evoke\Message\TreeIface,
	Evoke\View\ViewIface;

/**
 * Message Tree View
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View\Message
 */
class Tree implements ViewIface
{
	/**
	 * Attribs
	 * @var mixed[]
	 */
	protected $attribs;

	/**
	 * MessageTree
	 * @var Evoke\Message\TreeIface
	 */
	protected $messageTree;

	/**
	 * Construct a Message Tree view.
	 *
	 * @param Evoke\Message\TreeIface MessageTree.
	 * @param mixed[]                 Attribs.
	 */
	public function __construct(
		TreeIface $messageTree,
		Array     $attribs = array('class' => 'Message'))
	{
		$this->attribs     = $attribs;
		$this->messageTree = $messageTree;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view.
	 *
	 * @param mixed[] Parameters to the view.
	 */	
	public function get(Array $params = array())
	{
		$params += array('Start_Level' => 0);
		
		return array('div',
		             $this->attribs,
		             $this->buildElems($this->messageTree,
		                               $params['Start_Level']));
	}
   
	/*********************/
	/* Protected Methods */
	/*********************/

	/**
	 * Build the view of the MessageTree recursively.
	 *
	 * @param Evoke\Message\TreeIface The message tree to build the view from.
	 * @param int                     The level we are building.
	 */
	protected function buildElems(TreeIface $messageTree, $level)
	{
		/// @todo Fix the is_array check to something appropriate.
		throw new RuntimeException('FIX the TODO.');
		
		if (is_array($messageTree))
		{
			return $this->buildElems($messageTree->get(), $level);
		}
      
		$msgElems = array();

		if (is_array($messageTree))
		{
			$childLevel = $level + 1;
	 
			foreach ($messageTree as $msg)
			{
				$msgElems[] = array(
					'ul',
					array('class' => ' Level_' . $level),
					array(array_unshift($msg['Title'],
					                    $this->buildElems(
						                    $msg['Message'], $childLevel))));
			}
		} 
		else
		{
			$msgElems[] = array(
				'li',
				array('class' => ' Leaf Level_' . $level),
				$messageTree);
		}

		return $msgElems;
	}
}
// EOF