<?php
/**
 * Request
 *
 * @package Model
 */
namespace Evoke\Model\Mapper\Request;

use Evoke\HTTP\RequestIface,
	Evoke\Model\Mapper\ReadIface;

/**
 * Request
 *
 * @author Paul Young <evoke@youngish.homelinux.org>
 * @copyright Copyright (c) 2012 Paul Young
 * @license MIT
 * @package Model
 */
class Request implements ReadIface
{
	protected
		/**
		 * Ignored Keys in the request.
		 * @var string[]
		 */
		$ignoredKeys,
		
		/**
		 * Request
		 * @var RequestIface
		 */
		$request;

	/**
	 * Construct a Request object.
	 *
	 * @param RequestIface Request.
	 * @param string[]     Keys to ignore in the request.
	 */
	public function __construct(RequestIface $request,
	                            Array        $ignoredKeys = array())
	{
		$this->ignoredKeys = $ignoredKeys;
		$this->request     = $request;
	}

	/******************/
	/* Public Methods */
	/******************/

	/**
	 * Read some data from the storage mechanism.
	 *
	 * @param mixed[] The conditions to match in the mapped data.
	 */
	public function read(Array $params = array())
	{
		return array_diff($this->request->getQueryParams(),
		                  $this->ignoredKeys);
	}
}
// EOF