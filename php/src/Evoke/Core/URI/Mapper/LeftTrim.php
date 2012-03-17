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

	/** Check the uri to see if it matches.
	 *  \return \bool Whether the uri is matched.
	 */
	public function matches($uri)
	{
		return isset($uri[0]) && (strpos($this->characters, $uri[0]) !== false);
	}
   
	/** Return the parameters for the URI.
	 *  \return \array An empty array as the trim does not get any parameters.
	 */
	public function getParams($uri)
	{
		return array();
	}
   
	/** Get the response uri.
	 *  @param uri \string The URI to get the response from.
	 *  \return \string The uri trimmed appropriately.
	 */
	public function getResponse($uri)
	{
		return ltrim($uri, $this->characters);
	}
}
// EOF