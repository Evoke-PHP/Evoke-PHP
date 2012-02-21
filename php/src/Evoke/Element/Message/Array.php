<?php
namespace Evoke\Element\Message;

class Array extends Element
{
	public function __construct(Array $setup)
	{      
		$setup += array('Container_Attribs' => array('class' => 'Message_Container'),
		                'Element_Class'     => 'Message');

		parent::__construct($setup);
	}

	/******************/
	/* Public Methods */
	/******************/

	public function set(Array $data)
	{
		return parent::set(
			array('div',
			      $this->containerAttribs,
			      array('Children' => $this->buildElems($data))));
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
					array('Text' => $msg['Title'],
					      'Children' => $this->buildElems(
						      $msg['Message'], $childLevel)));
			}
		} 
		else
		{
			$msgElems[] = array(
				'li',
				array('class' => $this->elementClass . ' Leaf Level_' .
				      $level),
				array('Text' => $messageArray));
		}

		return $msgElems;
	}
}
// EOF