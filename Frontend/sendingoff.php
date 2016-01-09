<?php
//This script sends off the data via a POST to the Backend to be processed!
require("dbcon.php");

$sql = "SELECT * FROM `toscrape` WHERE done=false LIMIT 1";

$info = mysqli_query($conn, $sql);
$results = mysqli_fetch_assoc($info);
$eid=urlencode($results["eid"]);
$password=$results["pass"];
$name=$results["name"];
$email=urlencode($results["email"]);
$gradyear=$results["gradyear"];
$id=$results["id"];


 function curl_request_async($url, $params, $type='GET')
  {
      
      $post_string = $params;

      $parts=parse_url($url);

      $fp = fsockopen($parts['host'],
          isset($parts['port'])?$parts['port']:80,
          $errno, $errstr, 30);

      // Data goes in the path for a GET request
      if('GET' == $type) $parts['path'] .= '?'.$post_string;

      $out = "$type ".$parts['path']." HTTP/1.1\r\n";
      $out.= "Host: ".$parts['host']."\r\n";
      $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
      $out.= "Content-Length: ".strlen($post_string)."\r\n";
      $out.= "Connection: Close\r\n\r\n";
      // Data goes in the request body for a POST request
      if ('POST' == $type && isset($post_string)) $out.= $post_string;

      fwrite($fp, $out);
      fclose($fp);
  }

if (!($results === null)){

  $sql = "SELECT * FROM `server_status` WHERE busy=false LIMIT 1";
  $go = mysqli_query($conn, $sql);
  $list = mysqli_fetch_assoc($go);
  
  $theurl = "http://".$list["address"]."/scrape.php";

  $theparams1 = "eid=".$eid."&pass=".$password."&name=".$name."&email=".$email."&gradyear=".$gradyear."&key=we76r3f4phsdv87twef";
  
  curl_request_async($theurl, $theparams1);
  
  mysqli_free_result($results);
  
  $sql = "UPDATE `toscrape` SET done=true WHERE id=".$id;
  mysqli_query($conn, $sql);
}
else {
  echo "Nothing to be done!";
}



?>