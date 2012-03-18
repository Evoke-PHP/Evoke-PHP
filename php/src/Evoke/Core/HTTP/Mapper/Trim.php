<?php
namespace Evoke\Core\HTTP\Mapper;

/** A mapper to trim characters from the left side of the URI.
 */
class Trim extends Base
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
		return trim($uri, $this->characters);
	}

	/** Check the uri to see if it matches.
	 *  \return \bool Whether the uri is matched.
	 */
	public function matches($uri)
	{
		return (
			preg_match('/^[' . preg_quote($this->characters, '/') . ']+/', $uri) ||
			preg_match('/[' . preg_quote($this->characters, '/') . ']+$/', $uri));
	}	
}
// EOF