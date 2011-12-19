<?php


/// Element_Message_Array
class Element_Message_Array extends Element
{
   protected $setup;
   
   public function __construct(Array $setup)
   {      
      $this->setup = array_merge(
	 array('Container_Attribs' => array('class' => 'Message_Container'),
	       'Data'              => NULL,
	       'Element_Class'     => 'Message'),
	 $setup);

      if (!isset($this->setup['Data']))
      {
	 throw new InvalidArgumentException(__METHOD__ . ' needs Data');
      }
      
      parent::__construct(
	 array('div',
	       $this->setup['Container_Attribs'],
	       array('Children' => $this->buildElems($this->setup['Data']))));
   }

   
   /*******************/
   /* Private Methods */
   /*******************/

   /// Build our message array elements recursively.
   private function buildElems($messageArray, $level=0)
   {
      if ($messageArray instanceof Message_Array)
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
	       array('class' => $this->setup['Element_Class'] . ' Level_' .
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
            array('class' => $this->setup['Element_Class'] . ' Leaf Level_' .
		  $level),
            array('Text' => $messageArray));
      }

      return $msgElems;
   }
}

// EOF