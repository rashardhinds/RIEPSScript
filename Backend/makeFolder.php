<?php
//Folder creation on the server


//I realize there's a useless call to a function that doesn't exist, I had scraped that function, because I didn't think I'd need it. Security experts are probably shaking their heads!

$name=filter_var($_GET['folder']);
$person=filter_var($_GET['name']);


$curr = "/var/www/html/public/tmp";
$name = urldecode($name);
if (is_numeric($person)) {
    $curr = $curr.$person;
}

if (!isset($name)){
    echo "error";
}
else {
    $newFolder = "/var/www/html/public/".$name;
    if (rename($curr, $newFolder)) {
        $intoFolder = "/var/www/html/public/".$person.DIRECTORY_SEPARATOR.$name;
    }
}


?>