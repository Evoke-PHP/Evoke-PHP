<?php
namespace Evoke\View\Form\Input;

class Submit implements \Evoke\View\ViewIface
{
	/** @property $attribs
	 *  @array Attributes for the submit (type is set to submit regardless).
	 */
	protected $attribs;
	
	/** Construct an input submit view.
	 *  @param attribs @array Attributes for the input submit view.
	 */
	public function __construct(Array $attribs = array())
	{
		$attribs['type'] = 'submit';
		$this->attribs = $attribs;
	}
	
   /******************/
   /* Public Methods */
   /******************/

	/** Get the submit view with any further attributes specified.
	 *  @param params @array Any attributes that need to be overriden.
	 *  @return The view as a simple array.
	 */
	public function get(Array $params = array())
	{
		return array('input',
		             array_merge($this->attribs, $params));
	}
}
// EOF