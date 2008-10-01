<?php
Class DirectoryStructureComponent extends Object
{
  var $dir;
  var $subdirs;
  var $basepath;

  /*
   * Lists the contents of a directory.
   *possible filter values are: files, dirs. On other values it lists everything.
   */
  function ListDir($dir,$filter='files'){
    if(!is_dir($this->basepath.$dir)){
      die('Not in a directory!');
     }

     $files = array();

    $d = dir($this->basepath.$dir);
    while (false !== ($entry = $d->read())) {
      switch($filter) {
      case 'dirs':
	if(is_dir($this->basepath.$dir.$entry))
	  $files[] = $entry;
	break;
      case 'files':
	if(!is_dir($this->basepath.$dir.$entry))
	  $files[] = $entry;
	break;
      default:
	$files[] = $entry;
      }
    }
    $d->close();

    return $files;
  }

  /*
   * Gives back a tree view of a directory structure.
   * The starting directory is at $this->basepath.
   * if $subdirs is given, then only the given subdir-path will be added.
   */
  function readTree($rootName = 'root',&$subdirs){
    $tree = new Object();
    //    $tree->rootNode = new Object();
    $tree->name = $rootName;
    $tree->absPath = $this->basepath;
    $this->readSubTree(&$tree,&$subdirs);
    if(count($subdirs)==0) $subdirs = false;
    return $tree;
  }

  function readSubTree(&$actualFolderNode,&$subdirs){
    $actualAbsDir = $actualFolderNode->absPath;
    $dir = @opendir($actualAbsDir);
    if (!$dir) {return 0;}
    while ($entry = readdir($dir)){
      if($entry == '.' || $entry == '..')
	continue;
      elseif (is_dir($actualAbsDir."/".$entry)){
	$child = &new Object();
	$child->name = $entry;
	$child->absPath = $actualAbsDir.$entry."/";
	$actualFolderNode->childNodes[] = &$child;
	
	// We filter the subdirectories
	if(!$subdirs)
	  continue;
	elseif(count($subdirs) == 0)
	  $this->readSubTree($child,$subdirs);
	elseif($subdirs[0] == $entry){
	  array_shift($subdirs);
	  $this->readSubTree($child,$subdirs);
	}
      }
    }
    closedir($dir);
  } 
  

}

?>
