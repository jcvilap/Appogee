<?php
    //require("../connectionString.php");
    require("../../../inc/connectionString.php");
    
    //Delete Picture if exist
    if($_POST['hasPicture'] == "1")
    {
        unlink('../Images/' . $_POST['poleID'] . '.jpg');
    }
    
    $strSQL = "DELETE FROM Pole WHERE pole_ID = ?";
    $strSQLParams = $_POST['poleID'];
    if($stmtDelete = $mysqli->prepare($strSQL))
    {
        //bind parameters
        $stmtDelete->bind_param("s", $_POST['poleID']);
        //execute query
        if(!$stmtDelete->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "deletePole.php", $_POST['userID'], "exec sql");
            
            //close statement
            $stmtDelete->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to delete light pole. Please contact admin.";
            die(json_encode($response));
        }
        
        //close statement
        $stmtDelete->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "deletePole.php", $_POST['userID'], "prepare sql");
        
        $response["success"] = 0;
        $response["message"] = "Failed to delete light pole. Please contact admin.";
        die(json_encode($response));
    }
    
    //close connection
    $mysqli->close();
    
    $response["success"] = 1;
    die(json_encode($response));
?>