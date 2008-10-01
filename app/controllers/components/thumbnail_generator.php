<?
/*
 * Based on:
 * THUMBNAIL GENERATOR - v-2.0 - 29/09/2003
 * Andre Cupini - andre@neobiz.com.br
 *
 * Modified by Viktor Nagy, viktor.nagy@gmail.com
 * 
 * Licensed under GPL 2.0
 *
 * Usage:

$thm = new ThumbnailGeneratorComponent('150');

to write it to a file:
$thm->makeThumbFile($file,$output);

or to send it to the browser
$thm->makeThumbContent($file);

 *
 */

class ThumbnailGeneratorComponent extends Object
{

  var $size,$R,$G,$B,$drawBorder,$drawImgBorder;
  var $initialPixelCol,$initialPixelRow;
  var $type2function= array();

  /*
   * $size the width of the thumbnail
   */
    function thumbnailGenerator($size=150) {
        # Thumb size default
        $this->size    = $size;

        # String and color borders
        $this->R            = 0;    // Red
        $this->G            = 0;    // Green
        $this->B            = 0;    // Blue

        # Error border
        $this->drawBorder   = TRUE;

        # Image border
        $this->drawImgBorder= TRUE;

        # Supported files
        $this->setSupported(IMAGETYPE_GIF,  'gif');
        $this->setSupported(IMAGETYPE_JPEG, 'jpeg');
        $this->setSupported(IMAGETYPE_PNG,  'png');

        # Initial position of string
        $this->set('initialPixelCol', 3);
        $this->set('initialPixelRow', 2);
    }

    // Set values 
    function set($key, $value) {
        $this->$key = $value;
    }

    // Check if image type is supported
    function isSupported($file) {
        # Integer showing the type of the image
        $imageType  =  @exif_imageType($file);
        $ext        =& $this->type2function; 
	if(in_array($imageType,array_keys($ext)))
	  return true;
	else
	  return false;
    }

    // Return wich function will be used
    function retrieveType($file) {
        # image type

        $imageType  =  exif_imageType($file);
        $ext        =& $this->supportedExt;
	return $this->type2function[$imageType];
    }

    // supported images types
    function setSupported($value, $function) {
      $this->type2function[$value] = $function;
    }

    // Print string in line
    function writeLine($string, $width = FALSE) {
        if($width === FALSE) {
            $strLen = strlen($string);
            $width = ($strLen * 10) - ($strLen * 2.8);
        }
        $img		    = ImageCreate ($width+1, 16);
        $background     = ImageColorAllocate ($img, 255, 255, 255);
        $defaultColor   = ImageColorAllocate ($img, $this->R, $this->G, $this->B);
        if($this->drawBorder) ImageRectangle($img, 0, 0, $width, 15, $defaultColor);
        ImageString ($img, 3, $this->initialPixelCol, $this->initialPixelRow,  $string, $defaultColor);
        header("Content-type: image/png");
        ImagePNG($img);
    }

    // Generate thumbnail
    function makeThumb($file) {
        if(file_exists($file)) {
            # The original size of the image
            list ($width, $height) = GetImageSize ($file);
            # If not size
	    $size = $this->size; // Default size
	    $newWidth  = $this->size;
	    $newHeight = ($newWidth * $height) / $width;
	    $func   = 'imagecreatefrom'.$this->retrieveType($file);
	    $src    = $func($file);
	    $dst    = ImageCreateTrueColor ($newWidth, $newHeight);
	    ImageCopyResized ($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
	    return $dst;
        } else $this->writeLine("Image does not exists!");
    }

    // Resize the image, if any of its dimensions is higher than given
    function resizeImage($file,$maxWidth=400,$maxHeight=400){
      if(!$this->isSupported($file))
	return false;
      if(!is_writable($file))
	 return false;
      if(file_exists($file)) {
            # The original size of the image
            list ($width, $height) = GetImageSize ($file);
	    echo $width;
            # If not size
	    if($width>$maxWidth){
	      $ratio = $maxWidth / $width;
	      $width = $maxWidth;
	      $height = $height*$ratio;
	    }
	    if($height>$maxHeight){
	      $ratio = $maxHeight / $height;
	      $height = $maxHeight;
	      $width = $width*$ratio;
	    }
	    list ($origWidth, $origHeight) = GetImageSize ($file);
	    $func   = 'imagecreatefrom'.$this->retrieveType($file);
	    $src    = $func($file);
	    echo 'w: '.$width;
	    echo 'h: '.$height;
	    //	    $dst    = ImageCreateTrueColor ($width, $height);
	    //	    ImageCopyResized ($dst, $src, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);

	    //	    $func = 'image'.$this->retrieveType($file);
	    //	    return $func($this->makeThumb($file),$file);

	    return true;
      }
      else
	return 'false';
    }

    // Return the image to the browser (standard output)
    function makeThumbContent($file){
      if(!$this->isSupported($file))
	return false;
      header("Content-type: image/png");
      $func = 'image'.$this->retrieveType($file);
      $func($this->makeThumb($file));
    }

    // Write the image to a file
    function makeThumbFile($file,$output) {
      //      if(!is_writable($output))
      //die('A fájl nem írható');
      if(!$this->isSupported($file))
	return false;

      $func = 'image'.$this->retrieveType($file);
      return $func($this->makeThumb($file),$output);
    }
}
?>
