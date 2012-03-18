<?php
namespace Evoke\Core\HTTP\Mapper;

/** A mapper to strip unwanted characters from a request so that a response can
 *  be formed.  No parameters are matched by this class.
 */
class UpperCaseFirst extends Base
{
	/** @property $delimiters
	 *  \array of delimiters that specify where a new word begins that should
	 *  have its first letter made upper case.
	 */
	protected $delimiters;
	
	public function __construct(Array $setup)
	{
		$setup += array('Delimiters' => NULL);

		if (!is_array($setup['Delimiters']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Delimiters as array');
		}

		parent::__construct($setup);

		$this->delimiters = $setup['Delimiters'];
	}
   
	/******************/
	/* Public Methods */
	/******************/

	public function getParams($uri)
	{
		return array();
	}

	/** Split the string by the delimiters make the first letter uppercase and
	 *  then rejoin the string with the delimiters.
	 */
	public function getResponse($uri)
	{      
		foreach ($this->delimiters as $delimiter)
		{
			$parts = explode($delimiter, $uri);

			foreach ($parts as &$part)
			{
				$part = ucfirst($part);
			}

			$uri = implode($delimiter, $parts);
		}

		return $uri;
	}

	public function matches($uri)
	{
		foreach ($this->delimiters as $delimiter)
		{
			if (strpos($uri, $delimiter) !== false)
			{
				return true;
			}
		}
      
		return false;
	}
}
// EOF