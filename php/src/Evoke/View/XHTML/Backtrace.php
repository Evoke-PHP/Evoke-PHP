<?php
namespace Evoke\View\XHTML;

use Evoke\Model\Data\DataIface,
	Evoke\View\ViewIface;

class Backtrace implements ViewIface
{ 
	/** @property data
	 *  @object Data
	 */
	protected $data;

	/** @property attribs
	 *  @array Attribs
	 */
	protected $attribs;

	/** Construct a Backtrace object.
	 *  @param data            @object Data.
	 *  @param attribs         @array  Attribs.
	 */
	public function __construct(DataIface $data,
	                            Array     $attribs         = array('class' => 'Backtrace'))
	{
		$this->data            = $data;
		$this->attribs         = $attribs;
	}

	/******************/
	/* Public Methods */
	/******************/

	public function get(Array $params = array())
	{
		$listItems = array();
		
		foreach ($this->data as $level => $info)
		{			
			$stackLineElements = array(
				'span',
				array('class' => 'File'),
				empty($info['File']) ? '<internal>' : $info['File']);
		
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
			
			$listItems[] = array('li', array(), $stackLine);
		}

		return array('ol', $this->attribs, $listItems);
	}
}

// EOF
