<?php
// php scan.php SAFE|UNSAFE(default SAFE) LIMIT(default 100)
// php scan.php SAFE 1 1>scan.log 2>&1 &
// tail scan.log

/*
 * constants
 */
define('SERVER', 'localhost');
define('USER', 'root');
define('PASS', '');
define('DATABASE', 'scratchr_dev');
define('APP', '/var/www/scratch/app/');
define('JAVA_PATH', 'java');

/*
 * required funcitons
 */
function error_handler($errno, $errstr, $errfile, $errline) {
   fwrite(STDERR,"$errstr in $errfile on $errline\n");
   exit($errno);
}

function cout($string) {
  fwrite(STDOUT, "$string\n");
}

function cin() {
  return fgets(STDIN);
}

set_error_handler('error_handler');

/*
 * start operations
 */
cout('============================================');

//safe mode
$safe_mode = true;
if(isset($argv[1]) && $argv[1] == 'UNSAFE') {
    $safe_mode = false;
}
if($safe_mode) {
    cout('~SAFE MODE!');
}
define('DAT_FILENAME', 'scan_'.$safe_mode.'.dat');

//limit
$limit = 100;
if(isset($argv[2])) {
    $limit = intval($argv[2]);
}

cout('LIMIT '.$limit);

cout('');
cout('============================================');

$con = mysql_connect(SERVER, USER, PASS);
cout('Connected to database');

if(mysql_select_db(DATABASE, $con)) {
  cout('Database selected');
}
else {
  cout('Database not found');
}

//if the .dat file exists
if(!file_exists(DAT_FILENAME)) {
    touch(DAT_FILENAME);
}

//check the .dat file perms
if(!is_writable(DAT_FILENAME) || !is_readable(DAT_FILENAME)) {
    cout('Can not access '. DAT_FILENAME);
    exit;
}

//get projects
$projects = __get_projects();
if(empty($projects)) {
    cout('Something is wrong with the query');
}

while($project = mysql_fetch_array($projects)) {
    cout('');
    cout('============================================');
    
    __extract_data($project['id'], $project['user_id']);
    
    file_put_contents(DAT_FILENAME, $project['id']);

    cout('============================================');
}

//cool :)
cout('');
cout('I am done!');
mysql_close($con);
exit(0);

function __get_projects() {
    $last_scanned_id = file_get_contents(DAT_FILENAME);
    $where_clause = '';
    if($last_scanned_id) {
       $where_clause = 'WHERE id < ' . $last_scanned_id;
    }

    global $limit;
    $limit_clause = 'LIMIT '.$limit;

    $query = 'SELECT `id`, `user_id`, `based_on_pid`, `root_based_on_pid`, `remixes`, `remixer`'
            .' FROM `projects` ' . $where_clause
            .' ORDER BY id DESC ' . $limit_clause;
    cout('Executing: '.$query);
    return mysql_query($query);
}

function __extract_data($project_shared_id, $user_shared_id) {
    cout("Extraction Starts: PROJECT-ID: $project_shared_id, USER-ID: $user_shared_id");
    $sbfilepath = __get_sbfilepath($project_shared_id, $user_shared_id);
    //file does not exist, we should just give it a break :)
    if(empty($sbfilepath)) {
        return false;
    }

    //running main scratch analyzer and collecitng entries
    $entries = __run_scratch_analyzer($sbfilepath);
    __store_based_ons($project_shared_id, $user_shared_id, $entries);

    cout("Extraction Ends: PROJECT-ID: $project_shared_id, USER-ID: $user_shared_id");
    return true;
}

function __store_based_ons($project_shared_id, $user_shared_id, $entries) {
    if(empty($entries)) { return false; }

    //find based_on_pid and root_based_on_pid - reverse way
    $based_on_pid = 0;
    $root_based_on_pid = 0;
    $override_based_on= true;

    for($i = (count($entries) -1); $i >=0; $i--) {
        if(__is_empty($entries[$i])) {
            continue;
        }

        $entry = str_replace('!undefined!', '', $entries[$i]);
        list($date, $event, $projectname, $username, $author) = explode("\t", $entry);
        cout("Scanning: $event $projectname $username");

        if($event != 'share' || __is_empty($projectname)
            || __is_empty($username)) {
            continue;
        }

        //find out the user's id
        $parent_user = mysql_query('SELECT id FROM users WHERE username = "' . $username . '"');
        $parent_user = mysql_fetch_array($parent_user);
        $parent_uid  = $parent_user['id'];

        //find out the project's id
        $parent_project = mysql_query('SELECT id FROM projects WHERE user_id = '. $parent_uid 
                                    .' AND name = "' . $projectname . '"');
        $parent_project = mysql_fetch_array($parent_project);
        $parent_pid  = $parent_project['id'];

        //cout("Parent pid: $parent_pid");
        //cout("Uploaded pid: $project_shared_id");

        //uploaded project's id is not the same as parent's
        if(!empty($parent_pid)
        && $project_shared_id != $parent_pid) {
            //can override based_on, as we are not yet sure about it
            if($override_based_on) {
                //gotcha, parent is from different user
                if($user_shared_id != $parent_uid) {
                    $based_on_pid = $parent_pid;
                    $override_based_on= false;
                }
                else {
                    $based_on_pid = $parent_pid;
                }
            }

            $root_based_on_pid = $parent_pid;
        }
    }//end for

    if($root_based_on_pid) {
        $query = 'UPDATE `projects` SET'
            . ' `based_on_pid` = '. $based_on_pid .', `root_based_on_pid` = ' . $root_based_on_pid
            . ' WHERE `id` = ' . $project_shared_id;

        //heck ;)
        global $safe_mode;
        if(!$safe_mode) {
            mysql_query($query);
        }
        cout("SUCCESSFULLY STORED: $project_shared_id "
                ."is based on $based_on_pid and root is $root_based_on_pid");

        __update_remixes_remixer($root_based_on_pid);
        if($based_on_pid != $root_based_on_pid)  {
           __update_remixes_remixer($based_on_pid);
        }
    }
}

function __run_scratch_analyzer($sbfilepath) {
    $jar = APP."misc/historyextraction/ScratchAnalyzer.jar";
    return __run_analyzer($jar, $sbfilepath, 'h');
}

function __run_analyzer($jar, $sbfilepath, $arg) {
    $sbfilepath = escapeshellcmd($sbfilepath);
    $sbfilepath = '"'.$sbfilepath.'"';
    $jar        = escapeshellcmd($jar);

    $exec = JAVA_PATH . ' -jar ' . $jar . '  ' . $arg . ' ' . $sbfilepath;
    cout("Executing: $exec");
    unset($entries);
    exec("$exec 2>&1", $entries, $err);

    $output = join("\n", $entries);
    if($err || empty($entries)) {
        cout("Analyzer returns error: $output");
        return false;
    }
    //cout("Analyzer returns: $output");
    return $entries;
}

function __get_sbfilepath($project_shared_id, $user_shared_id) {
    $ppath = APP.'webroot/static/projects/';
    $powner = mysql_query('SELECT username FROM users WHERE id = ' . $user_shared_id);
    $powner = mysql_fetch_array($powner);
    
    $sbfilepath =  $ppath . $powner['username'] . "/" . $project_shared_id . ".sb";

    if (!file_exists($sbfilepath)) {
        cout(".SB NOT FOUND: $sbfilepath");
        return false;
    }
    cout(".SB FILE FOUND: $sbfilepath\n");
    return $sbfilepath;
}

function __update_remixes_remixer($pid) {
    $query = 'SELECT COUNT(*) AS remixes, COUNT(DISTINCT user_id) AS remixer'
            . ' FROM `projects`'
            . ' WHERE  based_on_pid = ' . $pid
            . ' OR root_based_on_pid = ' . $pid;
    $result = mysql_query($query);
    $project = mysql_fetch_array($result);

    $query = 'UPDATE `projects` SET'
            . ' `remixes` = '. $project['remixes'] .', `remixer` = ' . $project['remixer']
            . ' WHERE `id` = ' . $pid;

    //heck ;)
    global $safe_mode;
    if(!$safe_mode) {
        mysql_query($query);
    }
    
    cout('SUCCESSFULLY UPDATED: ' . $pid . ' has ' . $project['remixes']
            . ' remixes and ' . $project['remixer'] . ' remixer');
}

function __is_empty($var) {
    return ( ((is_null($var) || rtrim($var) == "") && $var !== false)
            || (is_array($var) && empty($var)) );
}
?>