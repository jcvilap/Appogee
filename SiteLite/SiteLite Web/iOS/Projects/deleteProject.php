<?php
    //require("../connectionString.php");
    require("../../../inc/connectionString.php");
    
    //Check if calculate area markers exist for this project. If so, delete them first
    $strSQL = "SELECT * FROM Lot_Area_Markers WHERE project_ID = ?";
    $strSQLParams = $_POST['projectID'];
    
    if($stmt = $mysqli->prepare($strSQL))
    {
        //bind parameters
        $stmt->bind_param("s", $_POST['projectID']);
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "deleteProject.php", $_POST['userID'], "exec sql, check if calculate area markers exist");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to delete project. Please contact admin.";
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
                    insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "deleteProject.php", $_POST['userID'], "exec sql, deleting calculate area markers");
                    
                    //close statement
                    $stmtDelete->close();
                    
                    $response["success"] = 0;
                    $response["message"] = "Failed to delete project. Please contact admin.";
                    die(json_encode($response));
                }
                
                //close statement
                $stmtDelete->close();
            }
            else
            {
                insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "deleteProject.php", $_POST['userID'], "prepare sql, deleting calculate area markers");
                
                $response["success"] = 0;
                $response["message"] = "Failed to delete project. Please contact admin.";
                die(json_encode($response));
            }
        }
        
        //close statement
        $stmt->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "deleteProject.php", $_POST['userID'], "prepare sql, check if calculate area markers exist");
        
        $response["success"] = 0;
        $response["message"] = "Failed to delete project. Please contact admin.";
        die(json_encode($response));
    }
    //DONE deleting Calculate area markers for this project**********************
    
    //Check if light pole markers exist for this project. If so, delete them first
    $strSQL = "SELECT pole_ID, hasPicture FROM Pole WHERE project_ID = ?";
    $strSQLParams = $_POST['projectID'];
    
    if($stmt = $mysqli->prepare($strSQL))
    {
        //bind parameters
        $stmt->bind_param("s", $_POST['projectID']);
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "deleteProject.php", $_POST['userID'], "exec sql, check light pole markers exist first");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to delete project. Please contact admin.";
            die(json_encode($response));
        }
        
        /* bind result variables */
        $stmt->bind_result($poleIDBind, $hasPictureBind);
        
        //Enables 'num_rows'
        $stmt->store_result();
        
        if($stmt->num_rows > 0)
        {
            $strSQL = "DELETE FROM Pole WHERE project_ID = ?";
            $strSQLParams = $_POST['projectID'];
            if($stmtDelete = $mysqli->prepare($strSQL))
            {
                //bind parameters
                $stmtDelete->bind_param("s", $_POST['projectID']);
                //execute query
                if(!$stmtDelete->execute())
                {
                    insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "deleteProject.php", $_POST['userID'], "exec sql, deleting light pole markers");
                    
                    //close statement
                    $stmtDelete->close();
                    
                    $response["success"] = 0;
                    $response["message"] = "Failed to delete project. Please contact admin.";
                    die(json_encode($response));
                }
                
                //close statement
                $stmtDelete->close();
            }
            else
            {
                insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "deleteProject.php", $_POST['userID'], "prepare delete sql for light pole markers");
                
                $response["success"] = 0;
                $response["message"] = "Failed to delete project. Please contact admin.";
                die(json_encode($response));
            }
            
            //Delete Light Pole Pictures if they exist
            while($row = $stmt->fetch())
            {
                if($hasPictureBind == 1)
                {
                    unlink('../Images/' . $poleIDBind . '.jpg');
                }
            }
            
        }
        
        //close statement
        $stmt->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "deleteProject.php", $_POST['userID'], "prepare sql, check if light pole markers exist");
        
        $response["success"] = 0;
        $response["message"] = "Failed to delete project. Please contact admin.";
        die(json_encode($response));
    }
    //DONE Checking deleting Light Pole Markers for this project**********************
    
    
    $strSQL = "DELETE FROM Project WHERE project_ID = ?";
    $strSQLParams = $_POST['projectID'];
    if($stmtDelete = $mysqli->prepare($strSQL))
    {
        //bind parameters
        $stmtDelete->bind_param("s", $_POST['projectID']);
        //execute query
        if(!$stmtDelete->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "deleteProject.php", $_POST['userID'], "exec sql, delete project");
            
            //close statement
            $stmtDelete->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to delete project. Please contact admin.";
            die(json_encode($response));
        }
        
        //close statement
        $stmtDelete->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "deleteProject.php", $_POST['userID'], "prepare sql, delete project");
        
        $response["success"] = 0;
        $response["message"] = "Failed to delete project. Please contact admin.";
        die(json_encode($response));
    }
    
    //close connection
    $mysqli->close();
    
    $response["success"] = 1;
    die(json_encode($response));
?>