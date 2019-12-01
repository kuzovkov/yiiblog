<?php
function sendErrorMessages($errorMessages)
{
?>
<html lang="en">
  <head>
    <meta charset="utf-8" />
  </head>
  <body>
    <?php echo $errorMessages; ?>
  </body>
</html>
<?php
}

if(class_exists('ZipArchive'))
{
  $zipErrors = array(
                ZipArchive::ER_OK => array("code"=>"ZipArchive::ER_OK", "text"=>"No error"),
                ZipArchive::ER_MULTIDISK => array("code"=>"ZipArchive::ER_MULTIDISK", "text"=>"Multi-disk zip archives not supported"),
                ZipArchive::ER_RENAME => array("code"=>"ZipArchive::ER_RENAME", "text"=>"Renaming temporary file failed"),
                ZipArchive::ER_CLOSE => array("code"=>"ZipArchive::ER_CLOSE", "text"=>"Closing zip archive failed"),
                ZipArchive::ER_SEEK => array("code"=>"ZipArchive::ER_SEEK", "text"=>"Seek error"),
                ZipArchive::ER_READ => array("code"=>"ZipArchive::ER_READ", "text"=>"Read error"),
                ZipArchive::ER_WRITE => array("code"=>"ZipArchive::ER_WRITE", "text"=>"Write error"),
                ZipArchive::ER_CRC => array("code"=>"ZipArchive::ER_CRC", "text"=>"CRC error"),
                ZipArchive::ER_ZIPCLOSED => array("code"=>"ZipArchive::ER_ZIPCLOSED", "text"=>"Containing zip archive was closed"),
                ZipArchive::ER_NOENT => array("code"=>"ZipArchive::ER_NOENT", "text"=>"No such file"),
                ZipArchive::ER_EXISTS => array("code"=>"ZipArchive::ER_EXISTS", "text"=>"File already exists"),
                ZipArchive::ER_OPEN => array("code"=>"ZipArchive::ER_OPEN", "text"=>"Can't open file"),
                ZipArchive::ER_TMPOPEN => array("code"=>"ZipArchive::ER_TMPOPEN", "text"=>"Failure to create temporary file"),
                ZipArchive::ER_ZLIB => array("code"=>"ZipArchive::ER_ZLIB", "text"=>"Zlib error"),
                ZipArchive::ER_MEMORY => array("code"=>"ZipArchive::ER_MEMORY", "text"=>"Malloc failure"),
                ZipArchive::ER_CHANGED => array("code"=>"ZipArchive::ER_CHANGED", "text"=>"Entry has been changed"),
                ZipArchive::ER_COMPNOTSUPP => array("code"=>"ZipArchive::ER_COMPNOTSUPP", "text"=>"Compression method not supported"),
                ZipArchive::ER_EOF => array("code"=>"ZipArchive::ER_EOF", "text"=>"Premature EOF"),
                ZipArchive::ER_INVAL => array("code"=>"ZipArchive::ER_INVAL", "text"=>"Invalid argument"),
                ZipArchive::ER_NOZIP => array("code"=>"ZipArchive::ER_NOZIP", "text"=>"Not a zip archive"),
                ZipArchive::ER_INTERNAL => array("code"=>"ZipArchive::ER_INTERNAL", "text"=>"Internal error"),
                ZipArchive::ER_INCONS => array("code"=>"ZipArchive::ER_INCONS", "text"=>"Zip archive inconsistent"),
                ZipArchive::ER_REMOVE => array("code"=>"ZipArchive::ER_REMOVE", "text"=>"Can't remove file"),
                ZipArchive::ER_DELETED => array("code"=>"ZipArchive::ER_DELETED", "text"=>"Entry has been deleted")
               );
  $currentDir = dirname(__FILE__);
  $zip = new ZipArchive;
  $openResult = $zip->open('mydbdiffo.zip');
  if ($openResult === TRUE) 
  {
    $extractionResult = $zip->extractTo($currentDir);
    $zip->close();
    if($extractionResult === TRUE)
    {
      unlink('mydbdiffo.zip');
      $redirectTo = "mydbdiffo.php";
      ?>
      <html lang="en">
        <head>
          <meta charset="utf-8" />
          <meta http-equiv="refresh" content="2; url=<?php echo $redirectTo; ?>" />
        </head>
        <body>
          <div>mydbdiffo.zip has been extracted. Your browser will be redirected to <?php echo $redirectTo; ?> in 2 secs.</div>
        </body>
      </html>
      <?php
    }
    else
    {
      sendErrorMessages("Unable to unzip mydbdiffo.zip. Extraction failed. Check the file permissions and modify them if necessary. Try to unzip mydbdiffo.zip manually.");
    }
  }
  else 
  {
    $errorMessages = "";
    $error = array_search($openResult, $zipErrors);
    if($error !== FALSE)
    {
      $errorMessages .= $error["code"].": ".$error["text"]."<br/>";
    }
    if(file_exists($currentDir.DIRECTORY_SEPARATOR."mydbdiffo.zip")===FALSE)
    {
      $errorMessages .= "Unable to open mydbdiffo.zip. mydbdiffo.zip is missing.<br/>";
      if(is_writeable($currentDir)===FALSE)
      {
        $errorMessages .= $currentDir." is not writeable.</br>";
      }
      $errorMessages .= "Check the file permissions of this directory and modify them if necessary, then reload mydbdiffo.php.<br/>";
      $errorMessages .= "If all else fails, download mydbdiffo.zip from https://dbdiffo.com/getmydbdiffozip.php and unzip it manually. :(<br/>";
    }
    else
    {
      $errorMessages .= "Unable to open mydbdiffo.zip. Download mydbdiffo.zip from https://dbdiffo.com/getmydbdiffozip.php and unzip it manually. :(<br/>";
    }
    sendErrorMessages($errorMessages);
  }
}
else
{
  sendErrorMessages("ZipArchive class is not installed on your server or not enabled. (It is available in PHP 5.2.0 or above.) Try to install it and/or enable it, then reload mydbdiffo.php. If all else fails, download mydbdiffo.zip from https://dbdiffo.com/getmydbdiffozip.php and unzip it manually. :(");
}
?>