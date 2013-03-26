<?php
namespace Evoke\View;

use Evoke\Model\Data\DataIface,
	RuntimeException;

/**
 * Fixed View
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Fixed extends View
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
	 * @return mixed[] The data for the view.
	 */
	public function get()
	{
		return $this->contents;
	}

	public function setData(DataIface $data)
	{
		throw new RuntimeException(
			'Data should only be set in the constructor for a fixed view.');
	}
}
// EOF
