<?php
namespace Evoke\View\Form\Input;

use Evoke\View\ViewIface;

/**
 * Submit
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package View
 */
class Submit implements ViewIface
{
	/**
	 * Attributes for the submit (type is set to submit regardless).
	 * @var string[]
	 */
	protected $attribs;
	
	/**
	 * Construct an input submit view.
	 *
	 * @param string[] Attributes for the input submit view.
	 */
	public function __construct(Array $attribs = array())
	{
		$attribs['type'] = 'submit';
		$this->attribs = $attribs;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Get the submit view with any further attributes specified.
	 *
	 *  @param string[] Any attributes that need to be overriden.
	 *
	 *  @return mixed[] The submit element.
	 */
	public function get(Array $params = array())
	{
		return array('input',
		             array_merge($this->attribs, $params));
	}
}
// EOF