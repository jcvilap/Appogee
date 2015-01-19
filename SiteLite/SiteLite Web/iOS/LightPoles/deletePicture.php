<?php
    
    //require("../connectionString.php");
    require("../../../inc/connectionString.php");

    unlink('../Images/' . $_POST['poleID'] . '.jpg');
        
    $response["success"] = 1;
    die(json_encode($response));

?>

