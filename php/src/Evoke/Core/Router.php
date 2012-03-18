<?php
namespace Evoke\Core;

abstract class Router implements Iface\Router
{
	/** @property $rules
	 *  \array of rules that the router uses to route.
	 */
	protected $rules;

	/// Construct the basics of a router.
	public function __construct(Array $rules=array())
	{
		$this->rules = $rules;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/// Reset the router rules.
	public function reset()
	{
		$this->rules[] = array();
	}
}
// EOF