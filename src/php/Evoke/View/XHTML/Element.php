<?php
/**
 * Element View
 *
 * @package View\XHTML
 */
namespace Evoke\View\XHTML;

use Evoke\View\Data;

/**
 * Element View
 *
 * @author    Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license   MIT
 * @package   View\XHTML
 */
class Element extends Data
{
	/**
	 * Attributes.
	 * @var mixed[]
	 */
	protected $attribs;

	/**
	 * Tag.
	 * @var string
	 */
	protected $tag;
	
	/**
	 * Child View.
	 * @var Data
	 */
	protected $viewChild;

	/**
	 * Construct a simple XHTML element view.
	 *
	 * @param string   Tag.
	 * @param string[] Attribs.
	 * @param Data     Child view.
	 */
	public function __construct(/* String */ $tag       = 'div',
	                            Array        $attribs   = array(),
	                            Data         $viewChild = NULL)
	{
		$this->attribs   = $attribs;
		$this->tag       = $tag;
		$this->viewChild = $viewChild;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view (of the data) to be written.
	 *
	 * @return mixed[] The view data.
	 */
	public function get()
	{
		$children = array();
		
		if (isset($this->data, $this->viewChild))
		{
 			foreach ($this->data as $data)
			{
                $this->viewChild->setData($data);
				$children[] = $this->viewChild->get();
			}
		}
		
		return array($this->tag, $this->attribs, $children);
	}
}
// EOF