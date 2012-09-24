<?php
namespace Evoke\View;

/**
 * Fixed View
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Fixed implements ViewIface
{
	/**
	 * Contents
	 * @var mixed
	 */
	protected $contents;

	/**
	 * Construct a Fixed object.
	 *
	 * @param mixed Contents.
	 */
	public function __construct(/* mixed */ $contents)
	{
		$this->contents = $contents;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the fixed view.
	 *
	 * @param mixed[] Ignored parameters.
	 */
	public function get(Array $params = array())
	{
		return $this->contents;
	}
}
// EOF
