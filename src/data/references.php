<?php

class Data_References extends Data
{
   protected $references;
   
   public function __construct(Array $setup)
   {
      $setup += array('App'        => NULL,
		      'References' => NULL);

      parent::__construct($setup);

      $this->setup['App']->needs(
	 array('Set' => array('References' => $setup['References'])));
      
      $this->references = $this->setup['References'];

      foreach ($this->references as $parentField => $dataContainer)
      {
	 $referenceName = $this->getReferenceName($parentField);

	 /// Create a publicly accessible property for all referenced data.
	 $this->$referenceName =& $dataContainer;
      }
   }

   /******************/
   /* Public Methods */
   /******************/

   public function setData($data)
   {
      $jKey = $this->setup['Joint_Key'];
      $data += array($jKey);

      foreach ($this->references as $referenceName => $parentField)
      {
	 $data[$jKey] += array($parentField => array());
	 $this->$referenceName->setData($data[$jKey][$parentField]);
      }
      
      $this->data = $data;
   }
   
   /*********************/
   /* Protected Methods */
   /*********************/

   /** Get the reference name that will be used for accessing the joint data
    *  from this object.  It should match our standard naming of properties
    *  (camel case) and not contain the final ID which is not needed.
    *  @param parentField \string The parent field for the joint data.
    *  \return \string The reference name.
    */
   protected function getReferenceName($parentField)
   {
      $nameParts = mb_split('_', $parentField);
      $lastPart = end($nameParts);

      // Remove any final id.
      if (mb_strlower($lastPart) === 'id')
      {
	 array_pop($nameParts);
      }

      $name = '';

      foreach ($nameParts as $part)
      {
	 $name .= $part;
      }

      return $name;
   }
}

// EOF