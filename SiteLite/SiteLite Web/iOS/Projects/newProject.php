<?php
    //require("../connectionString.php");
    require("../../../inc/connectionString.php");
    
    $strSQL = "INSERT INTO Project(project_name, user_ID, date_opened, power_cost_per_kWh, date_of_service, contact_name, contact_phone, contact_email, comments) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $strSQLParams = $_POST['projectName'] . ', ' . $_POST['userID'] . ', ' . date("Y-m-d H:i:s") . ', ' . $_POST['powerCost'] . ', ' . $_POST['dateOfService'] . ', ' . $_POST['contactName'] . ', ' . $_POST['contactPhone'] . ', ' . $_POST['contactEmail'] . ', ' . $_POST['comments'];
    
    if($stmt = $mysqli->prepare($strSQL))
    {
        //bind parameters for markers
        $stmt->bind_param("sssssssss", $_POST['projectName'], $_POST['userID'], date("Y-m-d H:i:s"), $_POST['powerCost'], $_POST['dateOfService'], $_POST['contactName'], $_POST['contactPhone'], $_POST['contactEmail'], $_POST['comments']);
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "newProject.php", $_POST['userID'], "exec sql");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to create new project. Please contact admin.";
            die(json_encode($response));
        }
        
        //close statement
        $stmt->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "newProject.php", $_POST['userID'], "prepare sql statement");
        
        $response["success"] = 0;
        $response["message"] = "Failed to create new project. Please contact admin.";
        die(json_encode($response));
    }
    
    
    $response["success"] = 1;
    $response["message"] = strval($mysqli->insert_id);
    die(json_encode($response));
?>