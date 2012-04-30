<?php
namespace Evoke\HTTP\MediaType;

use Evoke\Iface;
use Evoke\HTTP\MediaType\Rule;

/** HTTP Media Type Router builder.
 */
class RouterFactory implements Iface\HTTP\MediaType\RouterFactory
{
	/** @property $request
	 *  @objec Request
	 */
	protected $request;

	/** Construct a RouterFactory object.
	 *  @param request @object Request
	 */
	public function __construct(Iface\HTTP\Request $request)
	{
		$this->request = $request;
	}
	
	/******************/
	/* Public Methods */
	/******************/

	/** Build an HTML5 only router.
	 */
	public function buildHTML5Only()
	{
		$router = $this->buildRouter();
		$router->addRule(
			new Rule\Equivalent('HTML5', array('Subtype' => 'xhtml+xml',
			                                   'Type'    => 'application')));
		$this->addFallback($router, 'HTML5');
		return $router;
	}
	

	/** Build a JSON only router.
	 */
	public function buildJSONOnly()
	{
		$router = $this->buildRouter();
		$router->addRule(
			new Rule\Equivalent('JSON', array('Subtype' => 'json',
			                                  'Type'    => 'application')));
		$this->addFallback($router, 'JSON');
		
		return $router;
	}
	
	/** Build the standard HTTP Media Type Router that handles the common media
	 *  types.
	 *  @param fallback @string The output format to use as a fallback for the
	 *                          ALL (* / *) rule.
	 */
	public function buildStandard($fallback='HTML5')
	{
		$router = $this->buildRouter();
		$router->addRule(
			new Rule\Equivalent('HTML5', array('Subtype' => 'html',
			                                   'Type'    => 'text')));
		$router->addRule(
			new Rule\Equivalent('XHTML', array('Subtype' => 'xhtml+xml',
			                                   'Type'    => 'application')));
		$router->addRule(
			new Rule\Equivalent('JSON', array('Subtype' => 'json',
			                                  'Type'    => 'application')));
		$router->addRule(
			new Rule\Equivalent('XML', array('Subtype' => 'xml',
			                                 'Type'    => 'application')));
		$router->addRule(
			new Rule\Equivalent('TEXT', array('Subtype' => 'plain',
			                                  'Type'    => 'text')));
		$this->addFallback($router, $fallback);
		
		return $router;
	}

	/** Build a media type router that only serves text.
	 */
	public function buildTextOnly()
	{
		$router = $this->buildRouter();
		$router->addRule(
			new Rule\Equivalent('TEXT', array('Subtype' => 'plain',
			                                  'Type'    => 'text')));
		$this->addFallback($router, 'TEXT');
		return $router;
	}

	/** Build an XHTML only router.
	 */
	public function buildXHTMLOnly()
	{
		$router = $this->buildRouter();
		$router->addRule(
			new Rule\Equivalent('XHTML', array('Subtype' => 'xhtml+xml',
			                                   'Type'    => 'application')));
		$this->addFallback($router, 'XHTML');
		return $router;
	}
	
	/*********************/
	/* Protected Methods */
	/*********************/

	/** Add a fallback output method to match the all specifier.
	 *  @param router       @object MediaType Router.
	 *  @param outputFormat @string The output format.
	 */
	protected function addFallback(Iface\HTTP\MediaType\Router $router,
	                               /* String */                $outputFormat)
	{
		$router->addRule(
			new Rule\Equivalent($outputFormat, array('Subtype' => '*',
			                                         'Type'    => '*')));
	}

	/** Build a media type router without any rules.
	 */
	protected function buildRouter()
	{
		return new \Evoke\HTTP\MediaType\Router($this->request);
	}
}
// EOF