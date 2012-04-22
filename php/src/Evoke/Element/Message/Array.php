<?php
namespace Evoke\Element\Message;

class Array extends Element
{
	/** @property $elementClass
	 *  \string The class for an element of the message array.
	 */
	protected $elementClass;
	
	public function __construct(
		/*s*/ $elementClass='Message',
		Array $attribs=array('class' => 'Message_Container'),
		Array $pos=array())
	{
		if (!is_string($elementClass))
		{
			throw new \InvalidArgumentException(
				__METHOD__ . ' requires elementClass as string');
		}

		
		parent::__construct($attribs, $pos);

		$this->elementClass = $elementClass;
	}

	/******************/
	/* Public Methods */
	/******************/

	public function set(Array $data)
	{
		return parent::set(array('div',
		                         array(),
		                         $this->buildElems($data)));
	}
   
	/*******************/
	/* Private Methods */
	/*******************/

	/// Build our message array elements recursively.
	private function buildElems($messageArray, $level=0)
	{
		if ($messageArray instanceof Array)
		{
			return $this->buildElems($messageArray->get(), $level);
		}
      
		$msgElems = array();

		if (is_array($messageArray))
		{
			$childLevel = $level + 1;
	 
			foreach ($messageArray as $msg)
			{
				$msgElems[] = array(
					'ul',
					array('class' => $this->elementClass . ' Level_' .
					      $level),
					array(array_unshift($msg['Title'],
					                    $this->buildElems(
						                    $msg['Message'], $childLevel))));
			}
		} 
		else
		{
			$msgElems[] = array(
				'li',
				array('class' => $this->elementClass . ' Leaf Level_' . $level),
				$messageArray);
		}

		return $msgElems;
	}
}
// EOF