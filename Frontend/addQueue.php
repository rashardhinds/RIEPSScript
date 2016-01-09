<?php
//This file added the task to the DB to be queried and sent so the Backend can handle the rest...

$eid=filter_var($_POST['eid']);
$password=filter_var($_POST['pass']);
$name=filter_var($_POST['name']);
$email=filter_var($_POST['email']);
$gradyear=filter_var($_POST['gradyear']);
$access=filter_var($_POST['access']);
$done = false;

function checkAccess($a) {
    require("dbcon.php");
    $sql = "SELECT * FROM `access_codes` WHERE code='".$a."'";
    $query = mysqli_query($conn, $sql);
    $final = mysqli_num_rows($query);
    
    if ($final > 0){
        $sql = "SELECT * FROM `access_codes` WHERE code='".$a."'";
        $status = mysqli_query($conn, $sql);
        $results = mysqli_fetch_assoc($status);
        if ($results["status"] == "used"){
            return "Access Code has been used.";
        }
        else{
            return "good";
        }
    } else{
        return "Access Code Does Not Exist";
        if (!mysqli_query($con,$query))
        {
            die('Error: ' . mysqli_error($con));
        }
    }
}
$theResult = checkAccess($access);

if ($theResult !== "good") {
    echo $theResult;
}
else{
    $sql = "INSERT INTO toscrape (eid, pass, name, email, gradyear, done) VALUES ('".$eid."', '".$password."', '".$name."', '".$email."', '".$gradyear."', '".$done."')";
if (mysqli_query($conn, $sql)) {
    $update = "UPDATE `access_codes` SET status=\"used\" WHERE code=\"".$access."\"";
    if (mysqli_query($conn, $update)) {
      //Had to use curl- guess its reliable-----------------------
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "http://FRONTEND-SITE-HERE/sendingoff.php");
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_exec($ch);
      curl_close($ch);
      //------------------------------------------------------------
      echo "Success! You will recieve an email with your download link momentarily!";
    }
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
mysqli_close($conn);
}
?>