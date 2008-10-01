<?php
class ResizescriptController extends AppController
{
   var $name = 'Resizescript';
   var $uses = array();

   function resizedirbuddy()
   {
	$from_dir = WWW_ROOT . "static".DS."icons".DS."buddy".DS;

	$this->autoRender = false;
	set_time_limit(0);

	if ($handle = opendir($from_dir))
	{
      	      echo "Resizing directory '".$from_dir."'...<br><br>";
	      $ext_list = array();

	      while (false !== ($file = readdir($handle)))
	      {
		if(strpos($file,"med") && strpos($file,"default")===false)
		{
			$ext = $this->resizeImage($from_dir.$file);
			echo "Filename: ".$file." Extension: ".$ext."<br>";
			if(isset($ext_list[$ext]))
				$ext_list[$ext]++;
			else
				$ext_list[$ext]=1;
		}
    	      }

	      $keys = array_keys($ext_list);
	      echo "<br>";
	      for($i=0; isset($keys[$i]); $i++)
	      {
		echo $keys[$i].": ".$ext_list[$keys[$i]]."<br>";
	      }

	      closedir($handle);
	}
    }

    function resizedirgallery() {

	$from_dir = WWW_ROOT . "static".DS."icons".DS."gallery".DS;

	$this->autoRender = false;
	set_time_limit(0);

	if ($handle = opendir($from_dir))
	{
      	      echo "Resizing directory '".$from_dir."'...<br><br>";
	      $ext_list = array();

	      while (false !== ($file = readdir($handle)))
	      {
			if(strpos($file,".png") && strpos($file,"default")===false)
			{
				# resizeImage function fails
				$ext = $this->resizeImage($from_dir.$file, 0, false, 'gallery');
				echo "Filename: ".$file." Extension: ".$ext."<br>";
				if(isset($ext_list[$ext]))
					$ext_list[$ext]++;
				else
					$ext_list[$ext]=1;
			}
    	      }

	      $keys = array_keys($ext_list);
	      echo "<br>";
	      for($i=0; isset($keys[$i]); $i++)
	      {
		echo $keys[$i].": ".$ext_list[$keys[$i]]."<br>";
	      }

	      closedir($handle);
	}
   }
}
?>
