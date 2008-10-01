<?php
class MprojectTag extends AppModel
{
    var $name = 'MprojectTag';
    var $belongsTo = array('ProjectTag' => array('className' => 'ProjectTag'));

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
   function bindProjectTag($conditions = null, $order = null) {
        $this->bindModel(array(
        'belongsTo' => array(
            'ProjectTag' =>
                array('className' => 'ProjectTag'))));
    }
}
?>
