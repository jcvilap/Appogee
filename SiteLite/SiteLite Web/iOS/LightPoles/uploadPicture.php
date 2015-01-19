<?php
    
//require("../connectionString.php");
require("../../../inc/connectionString.php");
    
$uploaddir = '../Images/';
$poleID = basename($_FILES['userfile']['name']);
$fileName = $poleID.".jpg";
$uploadfile = $uploaddir . $fileName;

if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile))
{
    $response["success"] = 1;
}
else
{
    $response["success"] = 0;
    $response["message"] = "Failed to upload photo.";
}
    
die(json_encode($response));

?>

