<?php
namespace Evoke\View\XHTML;

use Evoke\Model\Data\DataIface,
	Evoke\View\ViewIface;

/**
 * ListElement
 *
 * @todo Make this generically useful.
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class ListElement implements ViewIface
{ 
	/**
	 * Data
	 * @var Evoke\Model\Data\DataIface
	 */
	protected $data;

	/**
	 * Attribs
	 * @var string[]
	 */
	protected $attribs;

	/**
	 * List Item Attribs
	 * @var string[]
	 */
	protected $liAttribs;

	/**
	 * Construct a OrderedList object.
	 *
	 * @param Evoke\Model\Data\DatatIface Data.
	 * @param string[]                    Attribs.
	 * @param Evoke\View\ViewIface        List Item View.
	 * @param string[] List Item          Attribs.
	 */
	public function __construct(DataIface $data,
	                            Array     $attribs,
	                            ViewIface $listItemView,
	                            Array     $liAttribs = array('class' => 'Row')
	                            )
	{
		$this->data      = $data;
		$this->attribs   = $attribs;
		$this->liAttribs = $liAttribs;
	}

	/******************/
	/* Public Methods */
	/******************/

	public function get(Array $params = array())
	{
		$listItems = array();
		
		foreach ($this->data as $item)
		{
			$listItems[] = array('li', $this->liAttribs, $item['Text']);			                     
		}

		return array('ol', $this->attribs, $listItems);
	}
}

// EOF
