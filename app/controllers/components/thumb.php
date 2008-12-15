<?php 
/**
 * Thumbnail Generator for CakePHP that uses phpThumb (http://phpthumb.sourceforge.net/)
 *
 * @package default
 * @author Nate Constant
 **/ 

class ThumbComponent{
	
	/**
	 * The mime types that are allowed for images
	 */
	var $allowed_mime_types = array('image/jpeg','image/pjpeg','image/gif','image/png');
	
	/**
	 * File system location to save thumbnail to.  ** Must be writable by webserver 
	 */
	var $image_location = 'img';
	
	/**
	 * Array of errors
	 */
	var $errors = array();
	
	/**
	 * Default width if not set
	 */
	var $width = 100;
	
	/**
	 * Default height if not set
	 */
	var $height = 100;
	
	/**
	 * do we zoom crop the image?
	 */
	var $zoom_crop = 1;//do not zoom crop
	
	/**
	 * The original image uploaded
	 * @access private
	 */
	var $file;
	
	var $controller;
	var $model;
	
	function startup( &$controller ) {
      $this->controller = &$controller;
    }
	
	/**
	 * This is the method that actually does the thumbnail generation by setting up 
	 * the parameters and calling phpThumb
	 *
	 * @return bool Success?
	 * @author Ashok Gond
	 **/
	 
	function generateThumb($ribbon_image,$text,$dir,$image_name,$dimension,$width,$height)
	{
		 $ribbon_image =WWW_ROOT.$this->image_location.DS.$ribbon_image;
		 
		 $fltr=array();
		 $fltrPrarameter = 'wmt|'.$text.'|10|'.$dimension.'|ffffff|arial.ttf|100|0|315';
		 array_push($fltr, $fltrPrarameter);
		 if(!file_exists($ribbon_image))
			return false;
			
			// Load phpThumb
			
			App::import('Vendor', 'example', array('file'=>'php_thumb'.DS.'phpthumb.class.php'));
		
		
		if(!file_exists(WWW_ROOT.$this->image_location.DS.$dir.DS.$image_name)) {
			$phpThumb = new phpthumb();
			
			$phpThumb->setSourceFilename($ribbon_image);		
			$phpThumb->setParameter('w', $width);
			$phpThumb->setParameter('h', $height);
			
			$phpThumb->setParameter('fltr', $fltr);
			$phpThumb->setParameter('f','gif');
		
			if($phpThumb->generateThumbnail()){
	
				if(!$phpThumb->RenderToFile(WWW_ROOT.$this->image_location.DS.$dir.DS.$image_name)){
	
				}
			} else {
	
				$ext = '';
				$this->addError('could not generate thumbnail');
				$this->addError(implode('; '."\n",$phpThumb->debugmessages));	
			}
			
			
		}
		
		// if we have any errors, remove any thumbnail that was generated and return false
		if(count($this->errors)>0){
	//		echo __LINE__;
	//		exit;
			if(file_exists(WWW_ROOT.DS.$this->image_location.DS.$dir.DS.$image_name)) {

			}
			return false;
		} else return true;
			
	}
	
	function addError($msg){
		$this->errors[] = $msg;
	}
	
	  
}
?>