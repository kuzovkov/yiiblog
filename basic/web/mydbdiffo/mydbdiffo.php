<?php
error_reporting(E_ERROR);

if (file_exists("mydbdiffo_classes.php"))
{
  include "mydbdiffo_classes.php";
}

$REDIRECT_TO_UNPACK_CODE = <<<'EOT'
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="refresh" content="2; url=mydbdiffo_unpack.php" />
  </head>
  <body>
    <div>mydbdiffo.zip has been saved. Your browser will be redirected to mydbdiffo_unpack.php in 2 secs.</div>
  </body>
</html>
EOT;

function generateTempFileName()
{
  $r = null;
  $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
  $len = strlen($chars);
  while(!isset($r))
  {
    $tmp = "";
    for ($i = 0; $i < 10; $i++) 
    {
      $tmp .= $chars[rand(0, $len - 1)];
    }
    $fn = "mydbdiffo_temp_".$tmp.".zzz";
    if(!file_exists($fn))
    {
      $r = $fn;
    }
  }
  return $r;
}

function downloadUnpack()
{
  $r = true;
  $data = array();
  $options = array(
      'http' => array(
          'method' => "GET",
          'content' => http_build_query($data)
      )
  );
  $context = stream_context_create($options);
  $result = file_get_contents("https://dbdiffo.com/getmydbdiffounpack.php", false, $context);
  if($result===false)
  {
    $r = false;
    echo "Cannot connect to dbdiffo.com (cannot execute file_get_contents function). Sorry. You cannot use this product. Consult with your web hosting service provider. :(";
  }
  else
  {
    if (file_put_contents("mydbdiffo_unpack.php", $result) === false)
    {
      $r = false;
      echo "mydbdiffo_unpack.php cannot be created (cannot execute file_put_contents function)! Maybe the web server don't have the required permissions to create/write files on the server.";
    }
  }
  return $r;
}

function getMyDbDiffoFileTimestamp()
{
  $data = array();
  $options = array(
      'http' => array(
          'method' => "GET",
          'content' => http_build_query($data)
      )
  );
  $context = stream_context_create($options);
  $result = file_get_contents("https://dbdiffo.com/mydbdiffo_timestamp.php", false, $context);
  return intval($result);
}

function downloadMyDbDiffoZipAndRedirect($code)
{
  $data = array();
  $options = array(
      'http' => array(
          'method' => "GET",
          'content' => http_build_query($data)
      )
  );
  $developerVersion = isset($_REQUEST["dev"]) && $_REQUEST["dev"] == "true" ? "true" : "false";
  $context = stream_context_create($options);
  $result = file_get_contents("https://dbdiffo.com/getmydbdiffozip.php?dev=" . $developerVersion, false, $context);
  file_put_contents("mydbdiffo.zip", $result);
  echo $code;
  exit;
}

function createTableIfDoesNotExist($dbms, $tableName)
{
  $r = true;
  $result = $dbms->executeQuery("SELECT 1 AS dummy_value FROM `".$tableName."`");
  if (!is_array($result))
  {
    $createTable = "CREATE TABLE `".$tableName."` ";
    $createTable .= "(";
    $createTable .= "set_id bigint NOT NULL AUTO_INCREMENT, ";
    $createTable .= "app_name varchar(50) NOT NULL, ";
    $createTable .= "set_name varchar(50) NOT NULL, ";
    $createTable .= "set_type varchar(20) NOT NULL COMMENT 'VARIABLE - The set_value contains the value of a variable.\nAPPLICATION - The set_value contains the XML definition of an M13E application.', ";
    $createTable .= "set_value longtext, ";
    $createTable .= "CONSTRAINT `pk_".$tableName."` PRIMARY KEY (set_id)";
    $createTable .= ") CHARSET=utf8mb4";
    $r = $dbms->executeUpdate($createTable);
    if($r===true)
    {    
      $createIndex = "CREATE INDEX `ix_".$tableName."_ant` ";
      $createIndex .= "ON `".$tableName."` (app_name, set_name, set_type)";
      $r = $dbms->executeUpdate($createIndex);
    
      if($r===true)
      {
        $lesiInsert = "INSERT INTO `".$tableName."` (app_name, set_name, set_type, set_value) VALUES ";
        $lesiInsert .= "('myDbDiffo', 'lastExecutedStepId', 'VARIABLE', null)";
        $dbms->executeUpdate($lesiInsert);
        
        if($r===true)
        {
          $modelInsert = "INSERT INTO `".$tableName."` (app_name, set_name, set_type, set_value) VALUES ";
          $modelInsert .= "('myDbDiffo', 'model', 'VARIABLE', null)";
          $dbms->executeUpdate($modelInsert);
          
          if($r===true)
          {
            $sqlsInsert = "INSERT INTO `".$tableName."` (app_name, set_name, set_type, set_value) VALUES ";
            $sqlsInsert .= "('myDbDiffo', 'sqls', 'VARIABLE', null)";
            $dbms->executeUpdate($sqlsInsert);
          }
        }
      }
    }
    
    if($r!==true)
    {
      // In case of any error we drop the table and its index.
      $dbms->executeUpdate("DROP INDEX `ix_".$tableName."_ant` ON `".$tableName."`");
      $dbms->executeUpdate("DROP TABLE `".$tableName."`");
    }
  }
  return $r;
}

// Returns an error message in case of error otherwise true.
function saveLastExecutedStepId($dbms, $tableName, $lesi)
{
  $r = true;
  $update = "UPDATE `".$tableName."` SET set_value = '".strval($lesi)."' "; // $lesi is an integer (always), so it is safe to use strval here.
  $update .= "WHERE app_name = 'myDbDiffo' AND set_name = 'lastExecutedStepId' AND set_type = 'VARIABLE'";
  $r = $dbms->executeUpdate($update);
  return $r;
}

// Returns the id or an error message.
function loadLastExecutedStepId($dbms, $tableName)
{
  $lesi = null;
  $select = "SELECT set_value FROM `".$tableName."` ";
  $select .= "WHERE app_name = 'myDbDiffo' AND set_name = 'lastExecutedStepId' AND set_type = 'VARIABLE'";
  $result = $dbms->executeQuery($select);
  if (is_array($result))
  {
    if(count($result)>0)
    {
      $row = $result[0];
      $v = $row["set_value"];
      $lesi = isset($v) ? intval($v) : "'lastExecutedStepId' is null in table ".$tableName.".";
    }
    else
    {
      $lesi = "Missing 'lastExecutedStepId' setting in table ".$tableName.".";
    }
  }
  else
  {
    $lesi = $result;
  }
  return $lesi;
}

// Returns the sqls as JSON-string or null.
function getSQLArrayJSONString($dbms, $tableName)
{
  $sqlArrayString = null;
  $select = "SELECT set_value FROM `".$tableName."` ";
  $select .= "WHERE app_name = 'myDbDiffo' AND set_name = 'sqls' AND set_type = 'VARIABLE'";
  $result = $dbms->executeQuery($select);
  if (is_array($result))
  {
    if(count($result)>0)
    {
      $row = $result[0];
      $sqlArrayString = $row["set_value"];
      if(!isset($sqlArrayString))
      {
        $sqlArrayString = "[]";
      }
    }
  }
  return $sqlArrayString;
}

// Returns an error message in case of error otherwise true.
function saveSQLs($dbms, $tableName, &$sqls)
{
  $r = true;
  $sqlArrayString = $dbms->escapeString(json_encode($sqls));
  $update = "UPDATE `".$tableName."` SET set_value = '".$sqlArrayString."' ";
  $update .= "WHERE app_name = 'myDbDiffo' AND set_name = 'sqls' AND set_type = 'VARIABLE'";
  $r = $dbms->executeUpdate($update);
  return $r;
}

// Returns true if the model data exists otherwise false.
function checkModel($dbms, $tableName)
{
  $exists = false;
  $select = "SELECT 'true' AS check_result FROM `".$tableName."` ";
  $select .= "WHERE app_name = 'myDbDiffo' AND set_name = 'model' AND set_type = 'VARIABLE' AND set_value IS NOT NULL AND LENGTH(set_value) > 0";
  $result = $dbms->executeQuery($select);
  if (is_array($result))
  {
    if(count($result)>0)
    {
      $row = $result[0];
      $exists = $row["check_result"] == "true";
    }
  }
  return $exists;
}

// Returns the model data or null if it does not exist.
function &loadModel($dbms, $tableName)
{
  $modelData = null;
  $select = "SELECT set_value FROM `".$tableName."` ";
  $select .= "WHERE app_name = 'myDbDiffo' AND set_name = 'model' AND set_type = 'VARIABLE' AND set_value IS NOT NULL AND LENGTH(set_value) > 0";
  $result = $dbms->executeQuery($select);
  if (is_array($result))
  {
    if(count($result)>0)
    {
      $row = $result[0];
      $modelData = $row["set_value"];
    }
  }
  return $modelData;
}

// Returns an error message in case of error otherwise true.
function saveModel($dbms, $tableName, &$modelData)
{
  $r = true;
  $md = $dbms->escapeString($modelData);
  $update = "UPDATE `".$tableName."` SET set_value = '".$md."' ";
  $update .= "WHERE app_name = 'myDbDiffo' AND set_name = 'model' AND set_type = 'VARIABLE'";
  $r = $dbms->executeUpdate($update);
  return $r;
}

function &getStreamContext(&$parameterArray)
{
  define('MULTIPART_BOUNDARY', '--------------------------' . microtime(true));
  $header = "";
  if (isset($_SESSION["dbDiffo_sessionId"]))
  {
    $header = "Cookie: PHPSESSID=" . $_SESSION["dbDiffo_sessionId"] . "\r\n";
  }
  $header .= 'Content-Type: multipart/form-data; boundary=' . MULTIPART_BOUNDARY;
  $content = "";
  foreach($parameterArray as $param)
  {
    $name = $param["name"];
    $value = isset($param["value"]) ? $param["value"] : null;
    $contentType = isset($param["contentType"]) ? $param["contentType"] : "text/plain";
    $fileName = isset($param["fileName"]) ? $param["fileName"] : null;
    $uploadedParameterName = isset($param["uploadedParameterName"]) ? $param["uploadedParameterName"] : null;
    $localFileName = isset($param["localFileName"]) ? $param["localFileName"] : null;
    
    $content .= "--" . MULTIPART_BOUNDARY . "\r\n" .
                'Content-Disposition: form-data; name="'.$name.'"'.(isset($fileName) ? '; filename="'.$fileName.'"' : "").'\r\n'.
                "Content-Type: ".$contentType."\r\n\r\n";

    if (isset($uploadedParameterName))
    {
      $value = file_get_contents($_FILES[$uploadedParameterName]["tmp_name"]);
    }
    else
    if (isset($localFileName))
    {
      $value = file_get_contents($localFileName);
    }
    
    if(isset($value))
    {
      $content .= $value . "\r\n";
    }
    else
    {
      $content .= "\r\n";
    }
  }      
  $content .= "--" . MULTIPART_BOUNDARY . "--\r\n";

  $options = array(
      'http' => array(
          'header' => $header . "\r\n",
          'method' => 'POST',
          'content' => $content
      )
  );
  $context = stream_context_create($options);
  return $context;
}

$tableName = "mydbdiffo_m13e";
$timestamp = 1572421718;

header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0 ");
session_start();

// DEAR USER,
// YOU CAN IMPLEMENT YOUR MODIFICATIONS IN mydbdiffo_modifications.php.
// IF THE FILE EXISTS THE PROGRAM WILL INCLUDE IT.
// 
// FOR EXAMPLE:
// - YOU CAN MODIFY THE VALUE OF $tableName IF YOU WANT TO STORE YOUR
//   MYDBDIFFO AND M13E SETTINGS IN A TABLE NAMED OTHER THAN mydbdiffo_m13e.
//   (IN THIS CASE YOU MUST RENAME THE TABLE mydbdiffo_m13e IN YOUR DB, TOO.)
// - BY SETTING $timestamp TO 4000000000 YOU CAN DISABLE SOFTWARE UPDATES.
// - BY SETTING $timestamp TO 0 THE SOFTWARE ALWAYS UPDATES ITSELF FROM 
//   dbdiffo.com.
if (file_exists("mydbdiffo_modifications.php"))
{
  include "mydbdiffo_modifications.php";
}


$result = null;

$myAction = isset($_REQUEST["_myaction"]) ? $_REQUEST["_myaction"] : null;
if (!file_exists("mydbdiffo_classes.php"))
{
  // download mydbdiffo_unpack.php file from dbdiffo.com and store it on the server
  if(downloadUnpack())
  {
    // download zip file from dbdiffo.com and store it on the server
    downloadMyDbDiffoZipAndRedirect($REDIRECT_TO_UNPACK_CODE);
  }
}
else
{
  if (!isset($myAction))
  {
    $ts = getMyDbDiffoFileTimestamp();
    if ($ts > $timestamp)
    {
      // download mydbdiffo_unpack.php file from dbdiffo.com and store it on the server
      if(downloadUnpack())
      {
        // download zip file from dbdiffo.com and store it on the server
        downloadMyDbDiffoZipAndRedirect($REDIRECT_TO_UNPACK_CODE);
      }
    }
    else
    {
      $result = file_get_contents("dbdiffo.php");
    }
  }
  else
  {
    // THESE ACTIONS DO NOT REQUIRE AN AUTHENTICATED LOCAL AND DBDIFFO SESSIONS.
    if ($myAction == "mykeepalive")
    {
      $result = "OK";
    }
    else
    if ($myAction == "myrefresh")
    {
      // download mydbdiffo_unpack.php file from dbdiffo.com and store it on the server
      if(downloadUnpack())
      {
        // download zip file from dbdiffo.com and store it on the server
        downloadMyDbDiffoZipAndRedirect($REDIRECT_TO_UNPACK_CODE);
      }
    }
    else
    if ($myAction == "mylogin")
    {
      $dbms = new \com\cc\odbd\ret\dbms\MySQL();
      $connectionResult = $dbms->connect($_REQUEST["mydbdiffo_hostname"], 
                                         intval($_REQUEST["mydbdiffo_port"]), 
                                         $_REQUEST["mydbdiffo_database"], 
                                         $_REQUEST["mydbdiffo_user"], 
                                         $_REQUEST["mydbdiffo_password"], 
                                         $_REQUEST["mydbdiffo_charset"]);
      if (!isset($connectionResult))
      {
        // store auth data in session
        $_SESSION["mydbdiffo_hostname"] = $_REQUEST["mydbdiffo_hostname"];
        $_SESSION["mydbdiffo_port"] = intval($_REQUEST["mydbdiffo_port"]);
        $_SESSION["mydbdiffo_database"] = $_REQUEST["mydbdiffo_database"];
        $_SESSION["mydbdiffo_user"] = $_REQUEST["mydbdiffo_user"];
        $_SESSION["mydbdiffo_password"] = $_REQUEST["mydbdiffo_password"];
        $_SESSION["mydbdiffo_charset"] = $_REQUEST["mydbdiffo_charset"];
        
        $createTableResult = createTableIfDoesNotExist($dbms, $tableName);
        if($createTableResult===true)
        {
          $result = "OK";
        }
        else
        {
          $result = "ERROR:".$createTableResult;
        }
        $dbms->disconnect();
      }
      else
      {
        $result = $connectionResult;
      }
    }
    else
    if ($myAction == "add_bugreport")
    {
      $parameters = array();
      $parameters[] = array("name" => "document", "fileName" => "document.xml", "uploadedParameterName" => "document", "contentType" => "text/xml");
      $parameters[] = array("name" => "log", "fileName" => "log.xml", "uploadedParameterName" => "log", "contentType" => "text/xml");
      $parameters[] = array("name" => "stackTrace", "fileName" => "stackTrace.txt", "uploadedParameterName" => "stackTrace");
      $parameters[] = array("name" => "comments", "fileName" => "comments.txt", "uploadedParameterName" => "comments");
      $parameters[] = array("name" => "name", "fileName" => "name.txt", "uploadedParameterName" => "name");
      $parameters[] = array("name" => "email", "fileName" => "email.txt", "uploadedParameterName" => "email");
      $parameters[] = array("name" => "userAgent", "fileName" => "userAgent.txt", "uploadedParameterName" => "userAgent");
      $parameters[] = array("name" => "version", "fileName" => "version.txt", "uploadedParameterName" => "version");
      $parameters[] = array("name" => "errorMessage", "fileName" => "errorMessage.txt", "uploadedParameterName" => "errorMessage");
      $context = &getStreamContext($parameters);
      $result = file_get_contents("https://dbdiffo.com/add_bugreport.php", false, $context);
    }
    else
    if ($myAction == "add_aexception")
    {
      $exData = array();
      $exData["exception"] = $_REQUEST["exception"];
      $exData["stackTrace"] = $_REQUEST["stackTrace"];
      $options = array(
          'http' => array(
              'method' => "POST",
              'content' => http_build_query($exData)
          )
      );
      $context = stream_context_create($options);
      $result = file_get_contents("https://dbdiffo.com/add_aexception.php", false, $context);
    }
    else
    if ($myAction == "mychecklogin")
    {
      $result = isset($_SESSION["mydbdiffo_user"]) ? "OK" : "NOT LOGGED IN";
    }
    else
    {
      if (strpos($myAction, "my", 0) === 0)
      {
        // THESE ACTIONS ARE PROCESSED ON THE LOCAL SERVER.
        if (isset($_SESSION["mydbdiffo_user"])) // DO WE HAVE AN AUTHENTICATED LOCAL SESSION?
        {
          // THESE ACTIONS REQUIRE AN AUTHENTICATED LOCAL SESSION.
          if ($myAction == "mysavelesi")
          {
            $sValue = $_REQUEST["lastexecutedstepid"];
            $lesi = isset($sValue) && strlen($sValue)>0 ? intval($sValue) : null;
            $dbms = new \com\cc\odbd\ret\dbms\MySQL();
            $dbms->connect($_SESSION["mydbdiffo_hostname"], 
                           $_SESSION["mydbdiffo_port"], 
                           $_SESSION["mydbdiffo_database"], 
                           $_SESSION["mydbdiffo_user"], 
                           $_SESSION["mydbdiffo_password"], 
                           "utf8mb4");
            $lesiUpdateResult = saveLastExecutedStepId($dbms, $tableName, $lesi);
            $dbms->disconnect();
            $result = $lesiUpdateResult===true ? "OK" : $lesiUpdateResult;
          }
          else
          if($myAction == "mygetfile")
          {
            if(isset($_REQUEST["type"]) && isset($_SESSION["mydbdiffo_uploaded_file"]) && isset($_REQUEST["filename"]))
            {
              header('Content-Disposition: attachment; filename="' . $_REQUEST["filename"] . '"');
              header("Pragma: public");
              header("Expires: 0"); // set expiration time
              header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

              $uploaded = isset($_SESSION["mydbdiffo_uploaded_file"]);
              if($uploaded)
              {
                $s = file_get_contents($_SESSION["mydbdiffo_uploaded_file"]);
                unlink($_SESSION["mydbdiffo_uploaded_file"]);
                unset($_SESSION["mydbdiffo_uploaded_file"]);

                if($_REQUEST["type"]=="document")
                {
                  header('Content-Type: text/xml');
                }
                else
                if($_REQUEST["type"]=="sqlScript")
                {
                  header('Content-Type: text/plain');
                }
                else
                if($_REQUEST["type"]=="diagramImage")
                {
                  header('Content-Type: image/png');
                  $data = explode( ',', $s); // "data:image/png;base64,..."
                  if(count($data)==2)
                  {
                    $s = base64_decode($data[1]);
                  }
                }
                $result = $s;
              }
            }
          }
          else
          if ($myAction == "mygetlesi")
          {
            $dbms = new \com\cc\odbd\ret\dbms\MySQL();
            $dbms->connect($_SESSION["mydbdiffo_hostname"], 
                           $_SESSION["mydbdiffo_port"], 
                           $_SESSION["mydbdiffo_database"], 
                           $_SESSION["mydbdiffo_user"], 
                           $_SESSION["mydbdiffo_password"], 
                           "utf8mb4");
            $lesi = loadLastExecutedStepId($dbms, $tableName);
            $dbms->disconnect();
            $result = isset($lesi) && is_numeric($lesi) ? strval($lesi) : "ERROR:".$lesi;
          }
          else
          if ($myAction == "myuploadfile")
          {
            $tempFileName = generateTempFileName();
            if(move_uploaded_file($_FILES["uploaded_file"]["tmp_name"], $tempFileName)===false)
            {
              $result = "ERROR";
            }
            else
            {
              $_SESSION["mydbdiffo_uploaded_file"] = $tempFileName;
              $result = "OK";
            }
          }
          else
          if ($myAction == "mygetsqls")
          {
            $dbms = new \com\cc\odbd\ret\dbms\MySQL();
            $dbms->connect($_SESSION["mydbdiffo_hostname"], 
                           $_SESSION["mydbdiffo_port"], 
                           $_SESSION["mydbdiffo_database"], 
                           $_SESSION["mydbdiffo_user"], 
                           $_SESSION["mydbdiffo_password"], 
                           "utf8mb4");
            $sqlArrayString = getSQLArrayJSONString($dbms, $tableName);
            $dbms->disconnect();
            if(isset($sqlArrayString))
            {
              $result = $sqlArrayString;
            }
            else
            {
              $result = "ERROR";
            }
          }
          else
          if ($myAction == "myexecutesqls")
          {
            if(isset($_SESSION["mydbdiffo_uploaded_file"]))
            {
              $result = "ERROR";
              $edbms = new \com\cc\odbd\ret\dbms\MySQL();
              $edbms->connect($_SESSION["mydbdiffo_hostname"], 
                              $_SESSION["mydbdiffo_port"], 
                              $_SESSION["mydbdiffo_database"], 
                              $_SESSION["mydbdiffo_user"], 
                              $_SESSION["mydbdiffo_password"], 
                              $_SESSION["mydbdiffo_charset"]);
              $dbms = new \com\cc\odbd\ret\dbms\MySQL();
              $dbms->connect($_SESSION["mydbdiffo_hostname"], 
                             $_SESSION["mydbdiffo_port"], 
                             $_SESSION["mydbdiffo_database"], 
                             $_SESSION["mydbdiffo_user"], 
                             $_SESSION["mydbdiffo_password"], 
                             "utf8mb4");
              $append = isset($_REQUEST["append"]) ? $_REQUEST["append"] == "true" : false;
              $sqlArrayString = null;
              if($append)
              {
                $sqlArrayString = getSQLArrayJSONString($dbms, $tableName);
              }
              if(!$append || isset($sqlArrayString))
              {
                $sqls = null;
                $newSQLs = json_decode(file_get_contents($_SESSION["mydbdiffo_uploaded_file"]), true);
                unlink($_SESSION["mydbdiffo_uploaded_file"]);
                unset($_SESSION["mydbdiffo_uploaded_file"]);
                
                if($append)
                {
                  $sqls = json_decode($sqlArrayString, true);
                  $sqls = array_merge($sqls, $newSQLs);
                }
                else
                {
                  $sqls = $newSQLs;
                }
                $saveResult = saveSQLs($dbms, $tableName, $sqls);
                if($saveResult===true)
                {
                  $sqlCount = count($sqls);
                  $i = 0;
                  $errorOccured = false;
                  while(!$errorOccured && $i<$sqlCount)
                  {
                    $sql = &$sqls[$i];
                    if($sql["executed"]===false)
                    {
                      $updateResult = $edbms->executeUpdate($sql["sql"]);
                      if($updateResult===true)
                      {
                        $sql["executed"] = true;
                        $sql["error"] = null;
                      }
                      else
                      {
                        $errorOccured = true;
                        $sql["error"] = $updateResult;
                      }
                    }
                    $i++;
                  }
                  $saveResult = saveSQLs($dbms, $tableName, $sqls);
                  if($saveResult===true)
                  {
                    $result = "OK";
                  }
                  else
                  {
                    $result = "ERROR:".$saveResult;
                  }
                }
                else
                {
                  $result = "ERROR:".$saveResult;
                }
              }
              $dbms->disconnect();
              $edbms->disconnect();
            }
          }
          else
          if ($myAction == "myreveng")
          {
            $dbms = new \com\cc\odbd\ret\dbms\MySQL();
            $dbms->connect($_SESSION["mydbdiffo_hostname"], 
                           $_SESSION["mydbdiffo_port"], 
                           $_SESSION["mydbdiffo_database"], 
                           $_SESSION["mydbdiffo_user"], 
                           $_SESSION["mydbdiffo_password"], 
                           $_SESSION["mydbdiffo_charset"]);
            $catalogFilter = array("def");
            $schemaFilter = array();
            $fv = array("catalog" => "def", "schema" => $_SESSION["mydbdiffo_database"]);
            $schemaFilter[] = $fv;
            $model = $dbms->getExportedModel($catalogFilter, $schemaFilter, null);
            $dbms->disconnect();
            header('Content-Type: text/xml');
            $result = $model->toXML()->asXML();
          }
          else
          if ($myAction == "mycheckmodel")
          {
            $dbms = new \com\cc\odbd\ret\dbms\MySQL();
            $dbms->connect($_SESSION["mydbdiffo_hostname"], 
                           $_SESSION["mydbdiffo_port"], 
                           $_SESSION["mydbdiffo_database"], 
                           $_SESSION["mydbdiffo_user"], 
                           $_SESSION["mydbdiffo_password"], 
                           "utf8mb4");
            $result = checkModel($dbms, $tableName) ? "OK" : "MISSING";
            $dbms->disconnect();
          }
          else
          if ($myAction == "mysavemodel")
          {
            if (isset($_SESSION["mydbdiffo_uploaded_file"]))
            {
              $modelData = file_get_contents($_SESSION["mydbdiffo_uploaded_file"]);
              unlink($_SESSION["mydbdiffo_uploaded_file"]);
              unset($_SESSION["mydbdiffo_uploaded_file"]);
              
              $dbms = new \com\cc\odbd\ret\dbms\MySQL();
              $dbms->connect($_SESSION["mydbdiffo_hostname"], 
                             $_SESSION["mydbdiffo_port"], 
                             $_SESSION["mydbdiffo_database"], 
                             $_SESSION["mydbdiffo_user"], 
                             $_SESSION["mydbdiffo_password"], 
                             "utf8mb4");
              $saveResult = saveModel($dbms, $tableName, $modelData);
              if($saveResult===true)
              {
                $result = "OK";
              }
              else
              {
                $result = "ERROR:".$saveResult;
              }
              $dbms->disconnect();
            }
          }
          else
          if ($myAction == "myopenmodel")
          {
            $dbms = new \com\cc\odbd\ret\dbms\MySQL();
            $dbms->connect($_SESSION["mydbdiffo_hostname"], 
                           $_SESSION["mydbdiffo_port"], 
                           $_SESSION["mydbdiffo_database"], 
                           $_SESSION["mydbdiffo_user"], 
                           $_SESSION["mydbdiffo_password"], 
                           "utf8mb4");
            $modelData = loadModel($dbms, $tableName);
            $dbms->disconnect();
            if(isset($modelData))
            {
              $result = $modelData;
            }
            else
            {
              $result = "ERROR";
            }
          }
        }
      }
      else
      {
        // THESE ACTIONS ARE PROCESSED ON THE DBDIFFO.COM SERVER.
        // MOST OF THESE ACTIONS DO NOT REQUIRE AN AUTHENTICATED LOCAL SESSION.
        // DBDIFFO SESSION IS (RE)CREATED WHEN THE CLIENT APP USES DBDIFFO SERVICES (MANAGED BY CLIENT APP).
        
        $oldDbDiffoSessionId = isset($_SESSION["dbDiffo_sessionId"]) ? $_SESSION["dbDiffo_sessionId"] : null;
        $options = array(
            'http' => array(
                'method' => "GET",
                'content' => http_build_query($data)
            )
        );
        if (isset($oldDbDiffoSessionId))
        {
          $options["http"]["header"] = "Cookie: PHPSESSID=" . $oldDbDiffoSessionId . "\r\n";
        }
        $context = stream_context_create($options);
        $dbDiffoSessionId = file_get_contents("https://dbdiffo.com/mysession.php", false, $context);
        $_SESSION["dbDiffo_sessionId"] = $dbDiffoSessionId;
        
        if (isset($dbDiffoSessionId) && $dbDiffoSessionId != $oldDbDiffoSessionId && isset($_SESSION["dbDiffo_loginEmail"]))
        {
          $loginUrl = "https://dbdiffo.com/login.php";
          $loginData = array();
          $loginData["loginEmail"] = $_SESSION["dbDiffo_loginEmail"];
          $loginData["loginPassword"] = $_SESSION["dbDiffo_loginPassword"];
          $options = array(
              'http' => array(
                  'method' => "POST",
                  'header' => "Cookie: PHPSESSID=" . $dbDiffoSessionId . "\r\n",
                  'content' => http_build_query($loginData)
              )
          );
          $context = stream_context_create($options);
          $result = file_get_contents($loginUrl, false, $context);
        }
        
        if($myAction=="genscript")
        {
          if (isset($_SESSION["mydbdiffo_user"]) && isset($_SESSION["mydbdiffo_uploaded_file"])) // DO WE HAVE AN AUTHENTICATED LOCAL SESSION?
          {
            $parameters = array();;
            if (isset($_REQUEST["beginindex"]))
            {
              $parameters[] = array("name" => "beginindex", "value" => $_REQUEST["beginindex"]);
            }
            if (isset($_REQUEST["endindex"]))
            {
              $parameters[] = array("name" => "endindex", "value" => $_REQUEST["endindex"]);
            }
            if (isset($_REQUEST["type"]))
            {
              $parameters[] = array("name" => "type", "value" => $_REQUEST["type"]);
            }
            if (isset($_REQUEST["parentid"]))
            {
              $parameters[] = array("name" => "parentid", "value" => $_REQUEST["parentid"]);
            }
            if (isset($_REQUEST["id"]))
            {
              $parameters[] = array("name" => "id", "value" => $_REQUEST["id"]);
            }
            $parameters[] = array("name" => "scripttype", "value" => $_REQUEST["scripttype"]);
            $parameters[] = array("name" => "scriptsettings", "value" => $_REQUEST["scriptsettings"]);
            $parameters[] = array("name" => "documentfile", "fileName" => "document.xml", "localFileName" => $_SESSION["mydbdiffo_uploaded_file"], "contentType" => "text/xml");
            $context = &getStreamContext($parameters);
            $result = file_get_contents("https://dbdiffo.com/genscript.php", false, $context);
            unlink($_SESSION["mydbdiffo_uploaded_file"]);
            unset($_SESSION["mydbdiffo_uploaded_file"]);
          }
        }
        else
        {
          $data = array();
          $url = null;
          switch ($myAction)
          {
            case "reginfo":
              $url = "reginfo.php?ts=" . $_REQUEST["ts"];
              $data["action"] = $_REQUEST["action"];
              break;
            case "opencloud":
              $url = "opencloud.php";
              $data["filename"] = $_REQUEST["filename"];
              break;
            case "listfiles":
              $url = "listfiles.php";
              break;
            case "userfiles":
              $url = "userfiles.php";
              break;
            case "savecloud":
              if (isset($_SESSION["mydbdiffo_user"]) && isset($_SESSION["mydbdiffo_uploaded_file"])) // DO WE HAVE AN AUTHENTICATED LOCAL SESSION?
              {
                $url = "savecloud.php";
                $data["filename"] = $_REQUEST["filename"];
                $data["type"] = $_REQUEST["type"];
                $data["data"] = file_get_contents($_SESSION["mydbdiffo_uploaded_file"]);
                unlink($_SESSION["mydbdiffo_uploaded_file"]);
                unset($_SESSION["mydbdiffo_uploaded_file"]);
              }
              break;
            case "login":
              $url = "login.php";
              $data["loginEmail"] = $_REQUEST["loginEmail"];
              $data["loginPassword"] = $_REQUEST["loginPassword"];
              break;
            case "register":
              $url = "register.php";
              $data["registerTitle"] = $_REQUEST["registerTitle"];
              $data["registerFirstname"] = $_REQUEST["registerFirstname"];
              $data["registerLastname"] = $_REQUEST["registerLastname"];
              $data["registerEmail"] = $_REQUEST["registerEmail"];
              $data["registerPassword"] = $_REQUEST["registerPassword"];
              $data["registerUsername"] = $_REQUEST["registerUsername"];
              $data["updated"] = $_REQUEST["updated"];
              break;
            case "update_settings":
              $url = "update_settings.php";
              $data["warnOnEditableChange"] = $_REQUEST["warnOnEditableChange"];
              $data["enableTutorialMode"] = $_REQUEST["enableTutorialMode"];
              $data["useFloatingWindows"] = $_REQUEST["useFloatingWindows"];
              break;
          }
          if (isset($url))
          {
            $url = "https://dbdiffo.com/" . $url;
            $options = array(
                'http' => array(
                    'method' => "POST",
                    'header' => "Cookie: PHPSESSID=" . $dbDiffoSessionId . "\r\n",
                    'content' => http_build_query($data)
                )
            );
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            if ($myAction == "login" && $result == "OK")
            {
              $_SESSION["dbDiffo_loginEmail"] = $_REQUEST["loginEmail"];
              $_SESSION["dbDiffo_loginPassword"] = $_REQUEST["loginPassword"];
            }
            else
            if ($myAction == "register" && $result == "OK")
            {
              $_SESSION["dbDiffo_loginEmail"] = $_REQUEST["registerEmail"];
              $_SESSION["dbDiffo_loginPassword"] = $_REQUEST["registerPassword"];
            }
          }
        }
      }
    }
  }
}
if (isset($result))
{
  echo $result;
}
?>