<?php


/// Image_Manip Manipulate images using Imagick.
class Image_Manip extends Image
{
   protected $setup;
   
   public function __construct(Array $setup)
   {
      $this->setup = array_merge(
	 array('App'         => NULL,
	       'Dir_Input'   => NULL,
	       'Dir_Output'  => NULL,
	       'File_Mode'   => 0750,
	       'File_System' => NULL,
	       'Image_Sizes' => NULL),
	 $setup);
      
      $this->setup['App']->needs(
	 array('Instance' => array(
		  'File_System' => $this->setup['File_System'],
		  'Image_Sizes' => $this->setup['Image_Sizes']),
	       'Set' => array(
		  'Dir_Input'  => $this->setup['Dir_Input'],
		  'Dir_Output' => $this->setup['Dir_Output'])));		  
   }

   /******************/
   /* Public Methods */
   /******************/
   
   public function scaleAll($original)
   {
      foreach ($this->setup['Image_Sizes'] as $format => $size)
      {
	 $this->scaleToSize($original, $size);
      }
   }

   public function scaleToFormat($original, $format)
   {
      $size = $this->setup['Image_Sizes']->get($format);
      $this->scaleToSize($original, $size);
   }
   
   public function scaleToSize($original, $size)
   {
      $image = new Imagick($this->setup['Dir_Input'] . $original);
      $image->scaleImage($size['Width'], $size['Height']);
      $outputFilename =
	 $this->setup['Dir_Output'] . $this->getImageName($original, $size);
     
      if (!$image->writeImage($outputFilename))
      {
	 throw new RuntimeException(
	    __METHOD__ . ' Unable to write image file from: ' .
	    $this->setup['Dir_Input'] . $original . ' to: ' .
	    $this->setup['Dir_Output'] . $outputFilename);
      }

      $this->setup['File_System']->chmod($outputFilename,
					 $this->setup['File_Mode']);
   }
}

// EOF