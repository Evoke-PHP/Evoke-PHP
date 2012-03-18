<?php
namespace Evoke\Core\HTTP\URI\Mapper;

/** HTTP URI Mapper class for mapping the received URI to a response.
 */
abstract class Base implements \Evoke\Core\Iface\HTTP\URI\Mapper
{
	/** @property $authoritative
	 *  \bool Whether the mapper can definitely give the final route for all
	 *  mappings that it matches.
	 */
	protected $authoritative;
   
	public function __construct(Array $setup)
	{
		$setup += array('Authoritative' => NULL);

		if (!is_bool($setup['Authoritative']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Authoritative as a bool');
		}

		$this->authoritative = $setup['Authoritative'];
	}
   
	/******************/
	/* Public Methods */
	/******************/
   
	public function isAuthoritative()
	{
		return $this->authoritative;
	}
}
// EOF