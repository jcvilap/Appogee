<?php

    $mysqli = new mysqli("localhost", "root", "arkonledmanagementsystem", "ArkonLED_db");
    
    //Check that connection was successful.
    if($mysqli->connect_errno)
    {
        //close connection
        $mysqli->close();
        
        $response["success"] = 0;
        $response["message"] = "Failed to connect. Please contact your admin.";
        die(json_encode($response));
    }
    
    function insertErrorTable($strSQL, $pageName, $userID, $description)
    {
        global $mysqli;
        
        $stmt = $mysqli->prepare("INSERT INTO SQLErrors(strSQL, pageName, userID, description) VALUES (?, ?, ?, ?)");
        
        //bind parameters for markers
        $stmt->bind_param("ssss", $strSQL, $pageName, $userID, $description);
        //execute query
        $stmt->execute();
        //close statement
        $stmt->close();
    }
?>