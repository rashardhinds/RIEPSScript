<?php
//This file is the main ignitiator of the script, the frontend will post data
//here. Security experts likely see something REALLY wrong here...I know I
//didn't sanatize any of the posted data BUT, I did include a static auth_key
//which the Frontend and Backend exchange, without it nothing will scrape!



$eid=urldecode($_GET['eid']);
$password=$_GET['pass'];
$person_name=$_GET['name'];
$email=urldecode($_GET['email']);
$gradyear=$_GET['gradyear'];
$auth_key=$_GET['key'];
$password=print_r($password, true);

//must require the sendgrid includes
require("sendgrid-php/sendgrid-php.php");

//creates the adfly links...
function adfly($url, $key, $uid, $domain = 'adf.ly', $advert_type = 'int')
{
  $api = 'http://api.adf.ly/api.php?';

  // api queries
  $query = array(
    'key' => $key,
    'uid' => $uid,
    'advert_type' => $advert_type,
    'domain' => $domain,
    'url' => $url
  );

  // full api url with query string
  $api = $api . http_build_query($query);
  // get data
  if ($data = file_get_contents($api))
    return $data;
}

//creates the bitly links...
function bitly_url_shorten($long_url, $access_token, $domain)
{
  $url = 'https://api-ssl.bitly.com/v3/shorten?access_token='.$access_token.'&longUrl='.urlencode($long_url).'&domain='.$domain;
  try {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $output = json_decode(curl_exec($ch));
  } catch (Exception $e) {
  }
  if(isset($output)){return $output->data->url;}
}

$path = "download/".$person_name."-".$eid;
$path1 = "/var/www/html/".$path;
$newPublicFolder = "/var/www/html/public";
$initial_check = file_exists($path1.".zip");
if ($auth_key === "we76r3f4phsdv87twef" && $initial_check !== true) { 
  if (mkdir($newPublicFolder, 0777)){
    //Would use this CURL to update the status of the server as it was processing
		//Had to use curl- guess its reliable-----------------------
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://FRONTEND-SITE-HERE/status_update.php?id=3&busy=1");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_exec($ch);
	curl_close($ch);
	//------------------------------------------------------------
      //A call to the shell to get a list of the classes...
      $output = shell_exec('casperjs sientot/getClasses.js "'.$eid.'" '.$password.' '.$gradyear.' 2>&1');
      $listClasses = preg_split('/\s+/', trim($output));
      
      foreach ($listClasses as &$value) {
          //for each of the classes, the actual frame of the page was removed, view that file for more info...
          $value = shell_exec('casperjs sientot/removeFrame.js "'.$eid.'" '.$password.' '.$value.' 2>&1');
      }
      unset($value);
      $assigments = [];
      foreach ($listClasses as &$value) {
          //Get the links to the assignments for each class...
          $output = shell_exec('casperjs sientot/assignmentLinks.js "'.$eid.'" '.$password.' "'.$value.'" '.$path.' 2>&1');
      }
      if (file_exists("/var/www/html/public/tmp")) {
        //This was an unnecessary if statement I didn't end up removing, was left over from testing...
        //It lead to redundant code below.
        if (rename("/var/www/html/public/tmp", "/var/www/html/public/untitled")) {
          sleep(30);
              // Function to remove folders and files
              $newname = "/var/www/html/".$person_name."-".$eid;
              if (rename($newPublicFolder, $newname)){
                $newloc = "/var/www/html/download/".$person_name."-".$eid;
                if (rename($newname, $newloc)) {
                  $officialpath = $newloc;
                  //Borrowed this ZIP script, will try and relocate the source on StackOverflow!
                  //This zipped the folder of the students tasks
                  $rootPath = realpath($officialpath);
                  
                  // Initialize archive object
                  $zip = new ZipArchive();
                  $zip->open($officialpath.".zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);
                  
                  // Create recursive directory iterator
                  /** @var SplFileInfo[] $files */
                  $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($rootPath),
                    RecursiveIteratorIterator::LEAVES_ONLY
                  );
                  
                  foreach ($files as $name => $file)
                  {
                    // Skip directories (they would be added automatically)
                    if (!$file->isDir())
                    {
                        // Get real and relative path for current file
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($rootPath) + 1);
                  
                        // Add current file to archive
                        $zip->addFile($filePath, $relativePath);
                    }
                  }
                  $zip->close();

                  //THERE ARE TWO POSSIBLE END POINTS
                  //THIS FIRST END POINT, WILL EMAIL THE STUDENT A DOWNLOAD LINK; WAS USED IN PRODUCTION TO STREAMLINE PROCESS
                  
                  /*$link = "http://BACKEND-SERVER-HERE/download.php?file=".$person_name."-".$eid.".zip";
                  
                  $adfly_link = adfly($link, "SECRET KEY", "SECRET");
                  
                  $bitly_link = bitly_url_shorten($link, "SECRET KEY", "bit.ly"); */
                  
                  //$output = shell_exec('casperjs sientot/sendEmail.js "'.$email.'" "'.$adfly_link.'" "'.$bitly_link.'" '.$person_name.' 2>&1');
                  //send_mail($email, $person_name, $adfly_link, $bitly_link);
                  /*$sendgrid = new SendGrid('SENDGRID KEY');
                  
                  $send_email = new SendGrid\Email();
                  
                  $shortened = print_r($bitly_link, true);
                  
                  $send_email
                    ->addTo($email)
                    ->setFrom('SCRIPT-EMAIL-WAS-HERE')
                    ->setSubject('Your RIEPS Files Are Ready!')
                    ->setText("Hey ".$person_name.",\n\nThank you for using the RIEPSScript! I hope this saved you a bunch of time. \n\n If you liked this service be sure to share it with people and let them know where to find it! Enjoy :)\n\n Your Download Link: ".$shortened."\n\n RIEPSScript on Stackdepot.com");
                  ;
                  
                  $sendgrid->send($send_email); */
                  //THIS IS THE SECOND POSSIBLE END POINT; WHERE THE SCRIPT RUNS ONLY ON THE SERVER SAVES THE FILES AND REPORTS ERRORS IF THERE ARE ANY
                  //THIS IS GOOD IF PLANNING A BUNCH ON FILES ONE AFTER ANOTHER
                  /*
              		$filename = "/var/www/html/public";
              		if (!file_exists($filename)) {
              			//Had to use curl- guess its reliable-----------------------
              			$ch = curl_init();
              			curl_setopt($ch, CURLOPT_URL, "http://FRONTEND-SITE-HERE/status_update.php?id=3&busy=0");
              			curl_setopt($ch, CURLOPT_HEADER, 0);
              			curl_exec($ch);
              			curl_close($ch);
              			//------------------------------------------------------------
              		} else {
              		$sendgrid = new SendGrid('SENDGRID KEY');
                                $send_email = new SendGrid\Email();
              			$send_email
                                  ->addTo('SYS ADMIN EMAIL')
                                  ->setFrom('SCRIPT-EMAIL-WAS-HERE')
                                  ->setSubject('Your RIEPS Files Are Ready!')
                                  ->setText("Public folder on server; Left off on: ".$eid);
                                ;
              			$sendgrid->send($send_email); 
              		}
                  */	
                  
                }
              }
        }
      }
      else {
        sleep(30);
              $newname = "/var/www/html/".$person_name."-".$eid;
              if (rename($newPublicFolder, $newname)){
                $newloc = "/var/www/html/download/".$person_name."-".$eid;
                if (rename($newname, $newloc)) {
                  $officialpath = $newloc;
                  
                  $rootPath = realpath($officialpath);
                  
                  // Initialize archive object
                  $zip = new ZipArchive();
                  $zip->open($officialpath.".zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);
                  
                  // Create recursive directory iterator
                  /** @var SplFileInfo[] $files */
                  $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($rootPath),
                    RecursiveIteratorIterator::LEAVES_ONLY
                  );
                  
                  foreach ($files as $name => $file)
                  {
                    // Skip directories (they would be added automatically)
                    if (!$file->isDir())
                    {
                        // Get real and relative path for current file
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($rootPath) + 1);
                  
                        // Add current file to archive
                        $zip->addFile($filePath, $relativePath);
                    }
                  }
                  $zip->close();

                  //THERE ARE TWO POSSIBLE END POINTS
                  //THIS FIRST END POINT, WILL EMAIL THE STUDENT A DOWNLOAD LINK; WAS USED IN PRODUCTION TO STREAMLINE PROCESS
                  
                  //$link = "http://BACKEND-SERVER-HERE/download.php?file=".$person_name."-".$eid.".zip";
                  
                  //$adfly_link = adfly($link, "SECRET KEY", "SECRET");
                  
                  //$bitly_link = bitly_url_shorten($link, "SECRET KEY", "bit.ly");
                  
                  //send_mail($email, $person_name, $adfly_link, $bitly_link);
                  //$output = shell_exec('casperjs sientot/sendEmail.js "'.$email.'" "'.$adfly_link.'" "'.$bitly_link.'" '.$person_name.' 2>&1');
                  
                  //$sendgrid = new SendGrid('SENDGRID KEY');
                  
                  //$send_email = new SendGrid\Email();
                  
                  //$shortened = print_r($bitly_link, true);
                  
                  /*$send_email
                    ->addTo($email)
                    ->setFrom('SCRIPT-EMAIL-WAS-HERE')
                    ->setSubject('Your RIEPS Files Are Ready!')
                    ->setText("Hey ".$person_name.",\n\nThank you for using the RIEPSScript! I hope this saved you a bunch of time. \n\n If you liked this service be sure to share it with people and let them know where to find it! Enjoy :)\n\n Your Download Link: ".$shortened."\n\n RIEPSScript on Stackdepot.com");
                  ;
                  
                  $sendgrid->send($send_email); */
                  //THIS IS THE SECOND POSSIBLE END POINT; WHERE THE SCRIPT RUNS ONLY ON THE SERVER SAVES THE FILES AND REPORTS ERRORS IF THERE ARE ANY
                  //THIS IS GOOD IF PLANNING A BUNCH ON FILES ONE AFTER ANOTHER
                /*
                $filename = "/var/www/html/public";
            		if (!file_exists($filename)) {
            			//Had to use curl- guess its reliable-----------------------
            			$ch = curl_init();
            			curl_setopt($ch, CURLOPT_URL, "http://FRONTEND-SITE-HERE/status_update.php?id=3&busy=0");
            			curl_setopt($ch, CURLOPT_HEADER, 0);
            			curl_exec($ch);
            			curl_close($ch);
            			//------------------------------------------------------------
            		} else {
                $sendgrid = new SendGrid('SENDGRID KEY');
                              $send_email = new SendGrid\Email();
            			$send_email
                                ->addTo('SYS ADMIN EMAIL')
                                ->setFrom('SCRIPT-EMAIL-WAS-HERE')
                                ->setSubject('Your RIEPS Files Are Ready!')
                                ->setText("Public folder on server; Left off on: ".$eid);
                              ;
            			$sendgrid->send($send_email); 
            		}
                */
                  echo "All Set! No Tmp renamed";
                } 
              }
            }
            unset($value);
          }
        }
?>
