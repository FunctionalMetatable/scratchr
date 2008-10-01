<?php
class P28nController extends AppController {
   var $name = 'P28n';
   var $uses = null;
   var $components = array('Cookie', 'P28n');

    function change($lang = null) {
        $this->P28n->change($lang);

        $this->redirect($this->referer(null, true));
    }
}
?>