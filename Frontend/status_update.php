<?php
//Updates the status of each server in the Database so data isnt sent to the same server while its processing...
//Each server has its own ID which must be configured on each server
require("dbcon.php");

$busy = $_GET['busy'];
$id = $_GET['id'];

//usage: http://FRONTEND-STIE-HERE/status_update.php?id=1&busy=0

$update = "UPDATE `server_status` SET busy=".$busy." WHERE server_id=".$id;

if (mysqli_query($conn, $update)) {
  echo "Status Updated!";
  if ($busy == 0) {
    //Had to use curl- guess its reliable-----------------------
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "http://FRONTEND-STIE-HERE/sendingoff.php");
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_exec($ch);
      curl_close($ch);
      //------------------------------------------------------------
  }
}
else {
  echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

?>