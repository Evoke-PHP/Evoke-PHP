<?php


class Data_Image_List extends Data
{
   public function __construct(Array $setup=array())
   {
      $setup += array('App'         => NULL,
		      'Dir'         => NULL,
		      'Empty_Image' => false,
		      'Formats'     => NULL,
		      'Image'       => NULL,
		      'Image_Sizes' => NULL,
		      'Numbered'    => false);
      
      parent::__construct($setup);

      $this->setup['App']->needs(
	 array('Set'      => array(
		  'Dir'      => $this->setup['Dir'],
		  'Formats'  => $this->setup['Formats'],
		  'Numbered' => $this->setup['Numbered']),
	       'Instance' => array(
		  'Image'       => $this->setup['Image'],
		  'Image_Sizes' => $this->setup['Image_Sizes'])));
   }
   
   /******************/
   /* Public Methods */
   /******************/

   public function getImagesEmpty($formats)
   {
      $images = array();
      $emptyImage = preg_replace('/%LANG%/',
				 $this->tr->getLanguage(),
				 $this->setup['Empty_Image']);
      
      foreach ($formats as $format)
      {
	 $size = $this->setup['Image_Sizes']->get($format);
	 $images[$format] = array($this->getName($emptyImage, $size));
      }

      return $images;
   }
   
   public function getImages()
   {
      $images = array();

      foreach ($this->setup['Formats'] as $format)
      {
	 $images[$format] = array();
	 $size = $this->setup['Image_Sizes']->get($format);
	 
	 foreach ($this->data as $num => $image)
	 {
	    $images[$format][] = $this->getImageName($image, $num, $size);
	 }
      }

      if (empty($images) && $this->setup['Empty_Image'] !== false)
      {
	 return $this->getImagesEmpty($this->setup['Formats']);
      }
	 
      return $images;
   }

   public function getImagesDefault()
   {
      return $this->getImagesByDefault(true);
   }

   public function getImagesNonDefault()
   {
      return $this->getImagesByDefault(false);
   }

   public function getImagesSingleFormatDefault($format)
   {
      $images = array();
      $size = $this->setup['Image_Sizes']->get($format);

      foreach ($this->data as $num => $image)
      {
	 if ($image['Is_Default'] == true)
	 {
	    $images[] = $this->getImageName($image, $num, $size);
	 }
      }

      if (empty($images) && $this->setup['Empty_Image'] !== false)
      {
	 $images = $this->getImagesEmpty($format);
	 $images = $images[$format];
      }

      return $images;
   }
   
   public function setData($data)
   {
      $this->data = $data;
   }

   /*********************/
   /* Protected Methods */
   /*********************/

   protected function getImageName($image, $number, $size)
   {
      $imageName = $this->setup['Dir'];
      
      if ($this->setup['Numbered'] === true)
      {
	 $imageName .= $number;
      }
      else
      {
	 $imageName .= $image['List_ID'] . '/' . $image['ID'];
      }

      $imageName .= '/' . $image['Image'];
      
      return $this->setup['Image']->getName($imageName, $size);
   }
   
   /*******************/
   /* Private Methods */
   /*******************/

   /** Get the images based on how the default value is set.
    *  @param \bool defaultValue Whether to return default or non-default
    *  images.
    */
   private function getImagesByDefault($defaultValue)
   {
      $images = array();

      foreach ($this->setup['Formats'] as $format)
      {
	 $images[$format] = array();
	 $size = $this->setup['Image_Sizes']->get($format);

	 foreach ($this->data as $num => $image)
	 {
	    // As we store a 0 or 1 for a bool in the database this comparison
	    // should not be strict.
	    if ($image['Is_Default'] == $defaultValue)
	    {
	       $images[$format][] = $this->getImageName($image, $num, $size);
	    }
	 }
      }

      return $images;
   }
}

// EOF