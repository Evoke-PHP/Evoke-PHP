<?php
namespace Evoke\Core\URI\Mapper;

abstract class Base implements \Evoke\Core\Iface\URI\Mapper
{
	/** @property $authoritative
	 *  \bool Whether the mapper can definitely give the final route for all URI
	 *  mappings that it receives.
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