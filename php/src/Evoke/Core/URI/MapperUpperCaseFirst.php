<?php
namespace Evoke\Core\URI;

/** A URI mapper to strip unwanted characters from a request so that a response
 *  can be formed.  No parameters are matched by this class.
 */
class MapperUpperCaseFirst extends Mapper
{
	public function __construct(Array $setup)
	{
		$setup += array('Delimiters' => NULL);

		if (!is_array($setup['Delimiters']))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires Delimiters as array');
		}

		parent::__construct($setup);
	}
   
	/******************/
	/* Public Methods */
	/******************/

	public function matches($uri)
	{
		foreach ($this->setup['Delimiters'] as $delimiter)
		{
			if (strpos($uri, $delimiter) !== false)
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

	/** Split the string by the delimiters make the first letter uppercase and
	 *  then rejoin the string with the delimiters.
	 */
	public function getResponse($uri)
	{      
		foreach ($this->setup['Delimiters'] as $delimiter)
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
}
// EOF