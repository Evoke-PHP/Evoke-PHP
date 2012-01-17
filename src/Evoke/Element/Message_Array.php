<?php
namespace Evoke;

class Element_Message_Array extends Element
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
	       $this->setup['Container_Attribs'],
	       array('Children' => $this->buildElems($data))));
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