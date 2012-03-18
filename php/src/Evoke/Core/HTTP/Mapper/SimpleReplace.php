<?php
namespace Evoke\Core\HTTP\Mapper;

/** A mapper to map a simple request to a response.
 *  This is for a request that does not have any parameters and only needs to
 *  be mapped to an appropriate Response class.
 */
class SimpleReplace extends Base
{
	/** @property $match
	 *  Regex match \string
	 */
	protected $match;

	/** @property $replacement
	 *  Regex replacement \string
	 */
	protected $replacement;
	
	public function __construct(Array $setup)
	{
		$setup += array('Match'       => NULL,
		                'Replacement' => NULL);

		if (!is_string($setup['Match']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Match as string');
		}

		if (!is_string($setup['Replacement']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Replacement as string');
		}
      
		parent::__construct($setup);

		$this->match       = $setup['Match'];
		$this->replacement = $setup['Replacement'];
	}
   
	/******************/
	/* Public Methods */
	/******************/

	public function getParams($uri)
	{
		return array();
	}
   
	public function getResponse($uri)
	{
		return preg_replace($this->match, $this->replacement, $uri);
	}

	public function matches($uri)
	{
		return (preg_match($this->match, $uri) > 0);
	}
}
// EOF