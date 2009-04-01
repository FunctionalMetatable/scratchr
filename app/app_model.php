<?php
/**
 * Application model for Cake.
 */
class AppModel extends Model{
	var $memcache = null;
	
    // var cacheQueries = false;
	
	/**
	 * TODO: implement queue for model saves so that
	 * all saves can be rolled back
	 */
	 
    /**
     * Before saving, check to see if
     * any custom validation methods have 
     * been declared of the form validate[FieldName]
     */
    function beforeSave() {
        /*
         $validateMethod = 'validate' . Inflector::camelize($field);
         if (method_exists(&$this, $validateMethod) {
             call_user_func(array(&$this, $validateMethod));
         }

         if ($this->validationErrors)
             return false;
         else
             return true;
         */
        return true;
    }
	
	
	function mc_connect() {
		$this->memcache = new Memcache();
		$this->memcache->connect('localhost', 11211) or die ("Could not connect");
	}
	
	function mc_get($str, $postfix = false) {
		return $this->memcache->get($this->__get_mc_key($str, $postfix));
	}
	
	//$ttl = mins
	function mc_set($str, $value, $postfix = false, $ttl = 0) {
		$ttl = $ttl * 60;
		return $this->memcache->set($this->__get_mc_key($str, $postfix), $value, false, $ttl) or die ("Failed to save data at the server");
	}
	
	function mc_delete($str, $postfix = false) {
        $this->memcache->delete($this->__get_mc_key($str, $postfix));
	}

    function mc_close() {
		return $this->memcache->close();
	}

    function __get_mc_key($str, $postfix) {
		$key = MEMCACHE_PREFIX .'-'. $str;
		if($postfix) {
			$key .= '-'.$postfix;
		}
		return $key;
	}
	
	/* Based on the URL used it will render different content */
	function getContentStatus() {
		$host = env('HTTP_HOST');
		if($host == FILTERED_HOST) {
			return 'safe';
		}
		return 'all';
	}

    //little hack to create mysql IN clause string from an array
    function createString($array) {
        $array = array_unique($array);
        return '("'.implode('" , "', $array).'")';
    }
}
?>
