<?php
namespace Evoke\Core\URI\Mapper;

/** A URI mapper to change strings from a request so that a response can be
 *  formed.  No parameters are matched by this class.
 */
class Strings extends Base
{
	/** @property $rules
	 *  The rules \array to be used for the mapping With each element having
	 *  a 'Match' and 'Replacement' value.
	 */
	protected $rules;

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

		$this->rules = $setup['Rules'];
	}
   
	/******************/
	/* Public Methods */
	/******************/

	public function matches($uri)
	{
		foreach ($this->rules as $rule)
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
		foreach ($this->rules as $rule)
		{
			$uri = str_replace($rule['Match'], $rule['Replacement'], $uri);
		}

		return $uri;
	}
}
// EOF