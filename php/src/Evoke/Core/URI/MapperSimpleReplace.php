<?php
namespace Evoke\Core\URI;

/** A URI mapper to map a simple request to a response.
 *  This is for a request that does not have any parameters and only needs to
 *  be mapped to an appropriate Response class.
 */
class MapperSimpleReplace extends Mapper
{
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
	}
   
	/******************/
	/* Public Methods */
	/******************/

	public function matches($uri)
	{
		return (preg_match($this->setup['Match'], $uri) > 0);
	}
   
	public function getParams($uri)
	{
		return array();
	}
   
	public function getResponse($uri)
	{
		return preg_replace($this->setup['Match'],
		                    $this->setup['Replacement'],
		                    $uri);
	}
}
// EOF