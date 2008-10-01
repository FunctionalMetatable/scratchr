<?php
/**
 * Application model for Cake.
 */
class AppModel extends Model{
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
}
?>
