<?php
    header("Access-Control-Allow-Origin: *");
    
    //require("../connectionString.php");
    require("../../../inc/connectionString.php");
    
    //Get User type (Admin, Sales Rep, etc.)
    $strSQL = "SELECT type FROM Users WHERE unique_user_id = ?";
    $strSQLParams = $_GET['userID'];
    if($stmt = $mysqli->prepare($strSQL))
    {
        //bind parameters
        $stmt->bind_param("s", $_GET['userID']);
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "getProjectNames.php", $_GET['email'], "exec sql, get user type");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to get projects. Please contact admin.";
            die(json_encode($response));
        }
        
        /* bind result variables */
        $stmt->bind_result($typeBind);
        
        //Enables 'num_rows'
        $stmt->store_result();
        
        if($stmt->num_rows > 0)
        {
            $row = $stmt->fetch();
        }
        //UserID doesn't exist in Users table
        else
        {
            $response["success"] = 0;
            $response["message"] = "Your account does not exist in the database.";
            die(json_encode($response));
        }
        
        //close statement
        $stmt->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "getProjectNames.php", "N/A", "prepare sql, get user's info by email");
        
        $response["success"] = 0;
        $response["message"] = "Failed to Failed to get projects. Please contact admin.";
        die(json_encode($response));
    }
    //DONE Getting User type ******************************************************************************
    
    
    if($typeBind == 2) //Admin
    {
        $strSQL = "SELECT P.project_ID, P.project_name, P.project_latitude, P.project_longitude, S.status_description, P.date_opened, P.lot_area, P.power_cost_per_kWh, P.date_of_service, P.contact_name, P.contact_phone, P.contact_email, P.city, P.state, P.comments, U.first_name, U.last_name, U.user_title FROM Project P INNER JOIN Project_Status_Types S ON P.status = S.status_type_id INNER JOIN Users U ON P.user_ID = U.unique_user_id ORDER BY P.date_opened DESC";
        $strSQLParams = "";
    }
    else //Sales Rep (3)
    {
        $strSQL = "SELECT P.project_ID, P.project_name, P.project_latitude, P.project_longitude, S.status_description, P.date_opened, P.lot_area, P.power_cost_per_kWh, P.date_of_service, P.contact_name, P.contact_phone, P.contact_email, P.city, P.state, P.comments, U.first_name, U.last_name, U.user_title FROM Project P INNER JOIN Project_Status_Types S ON P.status = S.status_type_id INNER JOIN Users U ON P.user_ID = U.unique_user_id WHERE user_ID = ? ORDER BY P.date_opened DESC";
        $strSQLParams = $_GET['userID'];
    }

    
    if($stmt = $mysqli->prepare($strSQL))
    {
        if($typeBind != 2) //Sales Rep
        {
            //bind parameters for markers
            $stmt->bind_param("s", $_GET['userID']);
        }
        
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "getProjectNames.php", $_GET['userID'], "exec sql update");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to get projects. Please contact admin.";
            die(json_encode($response));
        }
        
        /* bind result variables */
        $stmt->bind_result($projectIDBind, $projectNameBind, $latitudeBind, $longitudeBind, $statusDescriptionBind, $dateOpenBind, $lotAreaBind, $powerCostBind, $dateOfServiceBind, $contactNameBind, $contactPhoneBind, $contactEmailBind, $cityBind, $stateBind, $commentsBind, $salesFirstNameBind, $salesLastNameBind, $userTitleBind);
        
        $recordSet = array();
        while($row = $stmt->fetch())
        {
            $projectInfo["project_ID"] = $projectIDBind;
            $projectInfo["project_name"] = $projectNameBind;
            $projectInfo["project_latitude"] = $latitudeBind;
            $projectInfo["project_longitude"] = $longitudeBind;
            $projectInfo["status_description"] = $statusDescriptionBind;
            $projectInfo["date_opened"] = $dateOpenBind;
            $projectInfo["lot_area"] = $lotAreaBind;
            $projectInfo["power_cost_per_kWh"] = $powerCostBind / 100;
            $projectInfo["date_of_service"] = $dateOfServiceBind;
            $projectInfo["contact_name"] = $contactNameBind;
            $projectInfo["contact_phone"] = $contactPhoneBind;
            $projectInfo["contact_email"] = $contactEmailBind;
            $projectInfo["city"] = $cityBind;
            $projectInfo["state"] = $stateBind;
            $projectInfo["comments"] = $commentsBind;
            $projectInfo["first_name"] = $salesFirstNameBind;
            $projectInfo["last_name"] = $salesLastNameBind;
            $projectInfo["user_title"] = $userTitleBind;
            $recordSet[] = $projectInfo;
        }
        
        //close statement
        $stmt->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "updateProject.php", $_GET['userID'], "prepare sql, get info first");
        
        $response["success"] = 0;
        $response["message"] = "Failed to update project. Please contact admin.";
        die(json_encode($response));
    }
    
    
    //close connection
    $mysqli->close();
    
    $response["success"] = 1;
    $response["message"] = $recordSet;
    die(json_encode($response));
?>