<?php

class FileUploaderComponent extends Object {

  /**
   * Default validations
   * Here you can set the validations needed for the file upload.
   * Currently it supports only size and type.
   */
  var $validateFile = array('size' => 90800,'type' => 'pjpeg,jpg,jpeg,png,gif,x-png,bmp');

  function setValidation($validateFile=null) {
	$this->validateFile = $validateFile;
  }

  /**
   * $fileData - array of FILES['userfile']
   * $fileName - absolute file name or file directory if sameFileName is true
   */
  function handleFileUpload($fileData, $fileName, $sameFileName = false)
  {
    //Get file type
    $typeArr = explode('/', $fileData['type']);
    
    if($sameFileName)
    {
		$fileName .= $fileData['name'];
     }
	
	$error = array();
	$error[0] = false;
	$error[1] = $fileData['name'];

    //If size is provided for validation check with that size
    if ($this->validateFile['size'] && $fileData['size'] > $this->validateFile['size'])
    {
      $error[0] = 'File is too large to upload';
    }
    elseif ($this->validateFile['type'] && (isset($typeArr[1]) && (strpos($this->validateFile['type'], strtolower($typeArr[1])) === false)))
	{
		$error[0] = "Invalid file type";
    }
    else
    {
      //Data looks OK at this stage. Let's proceed.
      if (!$fileData['error'])
      {
        //Oops!! File size is zero. Error!
        if ($fileData['size'] == 0)
        {
          $error[0] = 'Zero size file found.';
        }
        else
        {
          if (is_uploaded_file($fileData['tmp_name']))
          {
            //Finally we can upload file now. Let's do it and return without errors if success in moving.
            if (!move_uploaded_file($fileData['tmp_name'], $fileName))
            {
              $error[0] = 'Cant move upload file to new location';
            }
          }
          else
          {
            $error[0] = 'File not uploaded file';
          }
        }
      }
	  else
	  {
	  	switch($fileData['error'])
		{
			case 1:
				$error[0] = 'The file is too large (server).';
			break;

			case 2:
				$error[0] = 'The file is too large (form).';
			break;

			case 3:
				$error[0] = 'The file was only partially uploaded.';
			break;

			case 4:
				$error[0] = 'No file was uploaded.';
			break;

			case 5:
				$error[0] = 'The servers temporary folder is missing.';
			break;

			case 6:
				$error[0] = 'Failed to write to the temporary folder.';
			break;
		}
	  }
    }
	return $error;
  }

  /**
   * Function to delete the uploaded file. $filename requires the full path of the file to be deleted.
   */
  function deleteMovedFile($fileName)
  {
    if (!$fileName || !is_file($fileName))
    {
      return true;
    }
    if(unlink($fileName))
    {
      return true;
    }
    return false;
  }
}

?>
