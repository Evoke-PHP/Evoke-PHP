<?php
namespace Evoke\Core\URI;

/** A URI mapper to change strings from a request so that a response can be
 *  formed.  No parameters are matched by this class.
 */
class MapperStrings extends Mapper
{
	public function __construct(Array $setup)
	{
		$setup += array('Rules' => NULL);

		if (!is_array($setup['Rules']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Rules as array');
		}

		foreach ($setup['Rules'] as $rule)
		{
			if (!isset($rule['Match'], $rule['Replacement']))
			{
				throw new \InvalidArgumentException(
					__METHOD__ . ' requires Rule with Match and Replacement.');
			}
		}	    
      
		parent::__construct($setup);
	}
   
	/******************/
	/* Public Methods */
	/******************/

	public function matches($uri)
	{
		foreach ($this->setup['Rules'] as $rule)
		{
			if (strpos($uri, $rule['Match']) !== false)
			{
				return true;
			}
		}
      
		return false;
	}
   
	public function getParams($uri)
	{
		return array();
	}
   
	public function getResponse($uri)
	{
		foreach ($this->setup['Rules'] as $rule)
		{
			$uri = str_replace($rule['Match'], $rule['Replacement'], $uri);
		}

		return $uri;
	}
}
// EOF