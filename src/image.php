<?php
class Image
{ 
   /******************/
   /* Public Methods */
   /******************/

   /** Convert a name from an existing size to another size.
    *  @param name \string The current name of the image.
    *  @param size \array The size for the new name.
    */
   public function convertName($name, $size)
   {
      return pathinfo($name, PATHINFO_DIRNAME) . '/' .
	 preg_replace('/(_w[0-9]+)?(_h[0-9]+)?$/',
		      $this->getDimensions($size),
		      pathinfo($name, PATHINFO_FILENAME)) .
	 '.' . pathinfo($name, PATHINFO_EXTENSION);
   }

   /** Get the name with dimension information for the image name.
    *  @param orig \string The original file (which should not contain the
    *  dimensions in the string).
    *  @size \array The size to make the image name represent.
    */
   public function getName($orig, $size)
   {
      return pathinfo($orig, PATHINFO_DIRNAME) . '/' .
	 pathinfo($orig, PATHINFO_FILENAME) . $this->getDimensions($size) .
	 '.' . pathinfo($orig, PATHINFO_EXTENSION);
   }

   /** Get the original filename for an image string with dimension information.
    *  @param name \string The image name.
    */
   public function getOriginal($name)
   {
      return pathinfo($name, PATHINFO_DIRNAME) . '/' .
	 preg_replace('/(_w[0-9]+)?(_h[0-9]+)?$/',
		      '',
		      pathinfo($name, PATHINFO_FILENAME)) .
	 '.' . pathinfo($name, PATHINFO_EXTENSION);
   }

   /*******************/
   /* Private Methods */
   /*******************/

   private function getDimensions($size)
   {
      $dimensions = '';
      
      if ($size['Width'] > 0)
      {
	 $dimensions .= '_w' . $size['Width'];
      }

      if ($size['Height'] > 0)
      {
	 $dimensions .= '_h' . $size['Height'];
      }

      return $dimensions;
   }
}
// EOF