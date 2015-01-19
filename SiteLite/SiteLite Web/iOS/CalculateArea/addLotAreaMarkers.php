<?php
    //require("../connectionString.php");
    require("../../../inc/connectionString.php");
    
    //Check if markers exist for this project. If so, delete them first
    $strSQL = "SELECT * FROM Lot_Area_Markers WHERE project_ID = ?";
    $strSQLParams = $_POST['projectID'];
    
    if($stmt = $mysqli->prepare($strSQL))
    {
        //bind parameters
        $stmt->bind_param("s", $_POST['projectID']);
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "addLotAreaMarkers.php", $_POST['userID'], "exec sql, check if markers exist");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to add lot area markers. Please contact admin.";
            die(json_encode($response));
        }
        
        //Enables 'num_rows'
        $stmt->store_result();
        
        if($stmt->num_rows > 0)
        {
            $strSQL = "DELETE FROM Lot_Area_Markers WHERE project_ID = ?";
            $strSQLParams = $_POST['projectID'];
            if($stmtDelete = $mysqli->prepare($strSQL))
            {
                //bind parameters
                $stmtDelete->bind_param("s", $_POST['projectID']);
                //execute query
                if(!$stmtDelete->execute())
                {
                    insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "addLotAreaMarkers.php", $_POST['userID'], "exec sql, deleting markers");
                    
                    //close statement
                    $stmtDelete->close();
                    
                    $response["success"] = 0;
                    $response["message"] = "Failed to add lot area markers. Please contact admin.";
                    die(json_encode($response));
                }
                
                //close statement
                $stmtDelete->close();
            }
            else
            {
                insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "addLotAreaMarkers.php", $_POST['userID'], "prepare sql, check if markers exist");
                
                $response["success"] = 0;
                $response["message"] = "Failed to add lot area markers. Please contact admin.";
                die(json_encode($response));
            }
        }
        
        //close statement
        $stmt->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "addLotAreaMarkers.php", $_POST['userID'], "prepare sql, check if markers exist");
        
        $response["success"] = 0;
        $response["message"] = "Failed to add lot area markers. Please contact admin.";
        die(json_encode($response));
    }
    //DONE Checking if previous markers exist for this project**********************
    
    
    //Update Total Area in Project Table
    $strSQL = "UPDATE Project SET lot_area = ? WHERE project_ID = ?";
    $strSQLParams = $_POST['area'] . ', ' . $_POST['projectID'];
    
    if($stmt = $mysqli->prepare($strSQL))
    {
        //bind parameters for markers
        $stmt->bind_param("ss", $_POST['area'], $_POST['projectID']);
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "addLotAreaMarkers.php", $_POST['userID'], "exec sql update. Area");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to update calculated area. Please contact admin.";
            die(json_encode($response));
        }
        
        //close statement
        $stmt->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "addLotAreaMarkers.php", $_POST['userID'], "prepare sql statement update. Area");
        
        $response["success"] = 0;
        $response["message"] = "Failed to update calculated area. Please contact admin.";
        die(json_encode($response));
    }
    
    //Insert area markers**********************************************
    //Split string
    $arrayMarkers = explode(",", $_POST['markers']);
    
    for($i = 0; $i < count($arrayMarkers); $i++)
    {
        //Split into latitude and longitude
        $arrayLatLong = explode("_", $arrayMarkers[$i]);
        $latitude = $arrayLatLong[0];
        $longitude = $arrayLatLong[1];
        
        $strSQL = "INSERT INTO Lot_Area_Markers(project_ID, marker_latitude, marker_longitude) VALUES (?, ?, ?)";
        $strSQLParams = $_POST['projectID'] . ', ' . $latitude . ', ' . $longitude;
        
        if($stmt = $mysqli->prepare($strSQL))
        {
            //bind parameters for markers
            $stmt->bind_param("sss", $_POST['projectID'], $latitude, $longitude);
            //execute query
            if(!$stmt->execute())
            {
                insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "addLotAreaMarkers.php", $_POST['userID'], "exec sql, insert new marker");
                
                //close statement
                $stmt->close();
                
                $response["success"] = 0;
                $response["message"] = "Failed to add lot area markers. Please contact admin.";
                die(json_encode($response));
            }
            
            //close statement
            $stmt->close();
        }
        else
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "addLotAreaMarkers.php", $_POST['userID'], "prepare sql statement, insert new marker");
            
            $response["success"] = 0;
            $response["message"] = "Failed to add lot area markers. Please contact admin.";
            die(json_encode($response));
        }
    }
    
    //close connection
    $mysqli->close();
    
    $response["success"] = 1;
    die(json_encode($response));
?>