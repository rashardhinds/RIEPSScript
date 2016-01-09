<?php
    //This file handled the downloads on the server, where the user would click the link, was "safer" than having a direct link
    function sanitize ($value){
		if( get_magic_quotes_gpc() )
			{
				$value = stripslashes( $value );
			}
		if( function_exists( “mysql_real_escape_string” ) )
			{
				$value = mysql_real_escape_string( $value );
			}
		else
			{
				$value = addslashes( $value );
			}
		return $value;
	}
    $file_name = sanitize($_GET["file"]);
    
    if (isset($file_name)) {
      $file = '/var/www/html/download/'.$file_name;
      if (file_exists($file)) {
          header('Content-Type: ' . mime_content_type($file_name));
          header('Content-Disposition: attachment;filename="' . basename($file_name) . '"');
          header('Content-Length: ' . filesize($file));
          readfile($file);
      } else {
          header('HTTP/1.1 404 Not Found');
      }
    }
    else {
      echo "Please check your link and try again!";
    }
    
?>