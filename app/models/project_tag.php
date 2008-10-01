<?php
class ProjectTag extends AppModel
{
    var $name = 'ProjectTag';
    var $belongsTo = array('Tag' => array('className' => 'Tag'), 'User' => array('className' => 'User'));

    /* Define associations
     * Seems that a project_tags table
     * is join table of the projects table
     * and the tags table. We don't
     * want multiple tags w/ same name associated
     * with the project, perhaps a tag count instead
     * otherwise this table will grow to infinity
     * very quickly. It should be that tags with same name are the same
     *
     * use hasManyAndBelongsTo
     */

   function bindProject() {
        $this->bindModel(array(
        'belongsTo' => array(
            'Project' => array('className' => 'Project'))));
   }

   function bindTag($conditions = null, $order = null) {
        $this->bindModel(array(
        'belongsTo' => array(
            'Tag' =>
                array('className' => 'Tag'))));
    }

    function unbindTag($condition = null, $order = null) {
        $this->unbindModel(array('belongsTo' => array('Tag')));
    }
}
?>
