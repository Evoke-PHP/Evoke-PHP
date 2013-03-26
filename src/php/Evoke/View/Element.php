<?php
namespace Evoke\View;

use InvalidArgumentException;

/**
 * An XHTML element view.
 */
class Element implements ViewIface
{
	/** Tag
	 * @var @string
	 */
	protected $tag;

	/** Attribs
	 * @var @array
	 */
	protected $attribs;

	/** Children
	 * @var @object
	 */
	protected $children;

	/**
	 * Construct a simple XHTML element view.
	 *
	 * @param string   Tag.
	 * @param string[] Attribs.
	 * @param mixed    Children.
	 */
	public function __construct(/* String */ $tag,
	                            Array        $attribs  = array(),
	                            /* Mixed  */ $children = NULL)
	{
		if (!is_string($tag))
		{
			throw new InvalidArgumentException('requires tag as string');
		}

		$this->tag      = $tag;
		$this->attribs  = $attribs;
		$this->children = $children;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the view (of the data) to be written.
	 *
	 * @param mixed   The data for the view.
	 * @param mixed[] Parameters for retrieving the view.
	 *
	 * @return mixed[] The view data.
	 */
	public function get($data = NULL, Array $params = array())
	{
		return array($this->tag, $this->attribs, $this->children);
	}
}
// EOF
