<?php
//This file ignitiated the scrape for eack task.

$link=$_GET['link'];
$eid=urldecode($_GET['eid']);
$password=$_GET['pass'];
$path=$_GET['path'];

$path = urldecode($path);
$link = urldecode($link);
$password = urldecode($password);


$ret = shell_exec('phantomjs --web-security=false sientot/scrapeIt.js "'.$eid.'" '.$password.' "'.$link.'" '.$path.' 2>&1');
if ($ret == "Not Valid"){
    echo "<h1 id=\"report\">Error on a task!</h1>";
    
}
else {
    echo "<h1 id=\"report\">All Set</h1>";
}
echo $ret;


?>
