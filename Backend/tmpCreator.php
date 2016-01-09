<?php


$path1 = "/var/www/html/public/tmp";
if (mkdir($path1, 0777)){
	//This is a useless good but it helped uring testing!
    echo "good";
}
else {
    $num = rand();
    mkdir($path1.$num, 0777);
    echo $num;
}

?>
