<?php
/**
 * Error processing for model validation
 * Calling this: $error->showMessage('User/name');
 */
    function showMessage($target) {
        list($model, $field) = explode('/', $target);
        if (isset($this->validationErrors[$model][$field]))
            return ($this->validationErrors[$model][$field]);
    }
    ?>
