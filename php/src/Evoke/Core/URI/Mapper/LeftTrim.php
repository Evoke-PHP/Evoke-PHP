<?php
namespace Evoke\Core\URI\Mapper;

/** A URI mapper to trim characters from the left side of the URI.
 */
class LeftTrim extends Base
{
	/** @property $characters
	 *  Characters to trim from the left side of the URI as a \string
	 */
	protected $characters;
	
	public function __construct(Array $setup)
	{
		$setup += array('Characters' => NULL);

		if (!is_string($setup['Characters']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Characters as string');
		}
      
		parent::__construct($setup);

		$this->characters = $setup['Characters'];
	}
   
	/******************/
	/* Public Methods */
	/******************/

	public function matches($uri)
	{
		return isset($uri[0]) && (strpos($this->characters, $uri[0]) !== false);
	}
   
	public function getParams($uri)
	{
		return array();
	}
   
	public function getResponse($uri)
	{
		return ltrim($uri, $this->characters);
	}
}
// EOF