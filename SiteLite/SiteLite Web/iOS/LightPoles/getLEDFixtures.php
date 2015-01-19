<?php
    //require("../connectionString.php");
    require("../../../inc/connectionString.php");
    
    //Shoebox
    $recordSetShoeBox = array();
    $strSQL = "SELECT * FROM LED_Fixture WHERE is_wallpack = 0";
    if($result = $mysqli->query($strSQL))
    {
        while($row = $result->fetch_assoc())
        {
            $recordSetShoeBox[] = $row;
        }
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:none', "getLEDFixtures.php", "none", "basic select * showbox");
        
        $response["success"] = 0;
        $response["message"] = "Failed to get LED Fixtures. Please contact admin.";
        die(json_encode($response));
    }
    
    //Wallpack
    $recordSetWallpack = array();
    $strSQL = "SELECT * FROM LED_Fixture WHERE is_wallpack = 1";
    if($result = $mysqli->query($strSQL))
    {
        while($row = $result->fetch_assoc())
        {
            $recordSetWallpack[] = $row;
        }
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:none', "getLEDFixtures.php", "none", "basic select * wallpack");
        
        $response["success"] = 0;
        $response["message"] = "Failed to get LED Fixtures. Please contact admin.";
        die(json_encode($response));
    }
    
    //free result set
    $result->close();
    //close connection
    $mysqli->close();
    
    
    $response["success"] = 1;
    $response["shoebox"] = $recordSetShoeBox;
    $response["wallpack"] = $recordSetWallpack;
    
    die(json_encode($response));
?>