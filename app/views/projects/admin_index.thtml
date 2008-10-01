<br class="ClearEm SpaceEm">
<div id="admin_all">
<h1>Scratch Administration</h1>

<h2>Current users are:</h2>
<div id="admin_currentusers">
<?php 
  foreach ($users as $person)
  {
    echo $person['User']['id'] . " name:" . $person['User']['firstname'] . ", " . $person['User']['lastname'] . " url:" . $person['User']['urlname'] . " username:". $person['User']['username']. " " .$person['User']['timestamp'] . "<p>";
  }
?></div>

<h2>Current projects are:</h2>
<div id="admin_currentprojects">
<?php
  foreach ($projects as $project)
  {
    echo "ProjectID: " . $project['Project']['id'] ." OwnerID: ".$project['Project']['user_id'] . " " . $project['Project']['name'] . " " . $project['Project']['timestamp'] . "<p>";
  }
?></div>

  <h2>Current comments are: </h2>
  <div id="admin_currentcomments">
  <?php
    foreach ($comments as $comment) 
    {
        echo "Project ID: ".$comment['Pcomment']['project_id'] . " UserID: " . $comment['Pcomment']['user_id'] . " " . $comment['Pcomment']['content'] . "<p>";
    }
    ?>
</div>
</div>
