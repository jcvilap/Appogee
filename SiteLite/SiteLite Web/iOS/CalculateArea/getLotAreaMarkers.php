<?php
    //require("../connectionString.php");
    require("../../../inc/connectionString.php");
    
    $strSQL = "SELECT marker_latitude, marker_longitude FROM Lot_Area_Markers WHERE project_ID = ? ORDER BY lot_area_marker_ID";
    $strSQLParams = $_GET['projectID'];
    
    if($stmt = $mysqli->prepare($strSQL))
    {
        //bind parameters for markers
        $stmt->bind_param("s", $_GET['projectID']);
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "getLotAreaMarkers.php", $_GET['userID'], "exec sql");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to load Calculate Area. Please contact admin.";
            die(json_encode($response));
        }
        
        /* bind result variables */
        $stmt->bind_result($latitudeBind, $longitudeBind);
        
        $recordSet = array();
        while($row = $stmt->fetch())
        {
            $markerInfo["latitude"] = $latitudeBind;
            $markerInfo["longitude"] = $longitudeBind;
            $recordSet[] = $markerInfo;
        }
        
        //close statement
        $stmt->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "getLotAreaMarkers.php", $_GET['userID'], "prepare sql");
        
        $response["success"] = 0;
        $response["message"] = "Failed to load Calculate Area. Please contact admin.";
        die(json_encode($response));
    }
    
    
    //close connection
    $mysqli->close();
    
    $response["success"] = 1;
    $response["message"] = $recordSet;
    die(json_encode($response));
?>