<?php
namespace Evoke\View;

use InvalidArgumentException;

/**
 * An XHTML element view.
 */
class Element extends View
{
	protected
		/** Child View
		 * @var ViewIface
		 */
		$viewChild;

	/**
	 * Construct a simple XHTML element view.
	 *
	 * @param string    Tag.
	 * @param string[]  Attribs.
	 * @param ViewIface Child view.
	 */
	public function __construct(/* String */ $tag       = 'div',
	                            Array        $attribs   = array(),
	                            ViewIface    $viewChild = NULL)
	{
		$this->params['Attribs'] = $attribs;
		$this->params['Tag']     = $tag;
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
		
		if (isset($this->viewChild))
		{
			foreach ($this->data as $data)
			{
				$children[] = $this->viewChild->get();
			}
		}
		
		return array($this->params['Tag'], $this->params['Attribs'], $children);
	}
}
// EOF
