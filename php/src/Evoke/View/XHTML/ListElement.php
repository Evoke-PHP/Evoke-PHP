<?php
namespace Evoke\View\XHTML;

use Evoke\Model\DataIface;

class ListElement extends XHTML
{ 
	/** @property data
	 *  @object Data
	 */
	protected $data;

	/** @property attribs
	 *  @array Attribs
	 */
	protected $attribs;

	/** @property liAttribs
	 *  @array LiAttribs
	 */
	protected $liAttribs;

	/** Construct a OrderedList object.
	 *  @param data      @object Data.
	 *  @param attribs   @array  Attribs.
	 *  @param liAttribs @array  LiAttribs.
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
