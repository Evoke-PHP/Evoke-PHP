<?php
namespace Evoke\View;

/**
 * Backtrace
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Backtrace extends View
{
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view of the backtrace.
	 *
	 * @return mixed[] The view.
	 */
	public function get()
	{
		$listItems = array();
		
		foreach ($this->data as $level => $info)
		{
			$stackLineElements = array(
				array('span',
				      array('class' => 'File'),
				      empty($info['File']) ? '<internal>' : $info['File']));
		
			if (!empty($info['Line']))
			{
				$stackLineElements[] = array(
					'span',
					array('class' => 'Line'),
					'(' . $info['Line'] . ')');
			}
			
			$stackLineElements[] = array(
				'span',	array('class' => 'Class'), $info['Class']);
			$stackLineElements[] = array(
				'span',	array('class' => 'Type'), $info['Type']);
			$stackLineElements[] = array(
				'span',	array('class' => 'Function'), $info['Function']);
			
			$listItems[] = array('li', array(), $stackLineElements);
		}

		return array('ol', array('class' => 'Backtrace'), $listItems);
	}
}
// EOF
