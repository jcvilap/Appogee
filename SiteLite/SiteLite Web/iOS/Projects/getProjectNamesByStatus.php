<?php
    
    header("Access-Control-Allow-Origin: *");
    
    //Check if Session variable is set
    /*session_start();
    if(!isset($_SESSION["userID"]))
    {
        $response["success"] = 0;
        $response["message"] = "Your session has expired. Log out then log back in.";
        die(json_encode($response));
    }*/
    
    require("../../../inc/connectionString.php");
    
    //ACTIVE Status
    if($_GET['userType'] == 2) //Admin
    {
        $strSQL = "SELECT P.project_ID, P.project_name, P.project_latitude, P.project_longitude, S.status_description, P.date_opened, P.lot_area, P.power_cost_per_kWh, P.date_of_service, P.contact_name, P.contact_phone, P.contact_email, P.city, P.state, P.comments, U.first_name, U.last_name FROM Project P INNER JOIN Project_Status_Types S ON P.status = S.status_type_id INNER JOIN Users U ON P.user_ID = U.unique_user_id WHERE P.status = 1 ORDER BY P.date_opened DESC";
    }
    else //Sales Rep
    {
        $strSQL = "SELECT P.project_ID, P.project_name, P.project_latitude, P.project_longitude, S.status_description, P.date_opened, P.lot_area, P.power_cost_per_kWh, P.date_of_service, P.contact_name, P.contact_phone, P.contact_email, P.city, P.state, P.comments, U.first_name, U.last_name FROM Project P INNER JOIN Project_Status_Types S ON P.status = S.status_type_id INNER JOIN Users U ON P.user_ID = U.unique_user_id WHERE user_ID = ? AND P.status = 1 ORDER BY P.date_opened DESC";
    }
    
    $strSQLParams = $_GET['userID'];
    
    
    if($stmt = $mysqli->prepare($strSQL))
    {
        //bind parameters for markers
        if($_GET['userType'] == 3)//Sales Rep
        {
            $stmt->bind_param("s", $_GET['userID']);
        }
        
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "getProjectNamesByStatus.php", $_GET['userID'], "exec sql active");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to get projects. Please contact admin.";
            die(json_encode($response));
        }
        
        /* bind result variables */
        $stmt->bind_result($projectIDBind, $projectNameBind, $latitudeBind, $longitudeBind, $statusDescriptionBind, $dateOpenBind, $lotAreaBind, $powerCostBind, $dateOfServiceBind, $contactNameBind, $contactPhoneBind, $contactEmailBind, $cityBind, $stateBind, $commentsBind, $salesFirstNameBind, $salesLastNameBind);
        
        $recordSetActive = array();
        while($row = $stmt->fetch())
        {
            $projectInfo["project_ID"] = $projectIDBind;
            $projectInfo["project_name"] = $projectNameBind;
            $projectInfo["project_latitude"] = $latitudeBind;
            $projectInfo["project_longitude"] = $longitudeBind;
            $projectInfo["status_description"] = $statusDescriptionBind;
            $projectInfo["date_opened"] = $dateOpenBind;
            $projectInfo["lot_area"] = $lotAreaBind;
            $projectInfo["power_cost_per_kWh"] = $powerCostBind;
            $projectInfo["date_of_service"] = $dateOfServiceBind;
            $projectInfo["contact_name"] = $contactNameBind;
            $projectInfo["contact_phone"] = $contactPhoneBind;
            $projectInfo["contact_email"] = $contactEmailBind;
            $projectInfo["city"] = $cityBind;
            $projectInfo["state"] = $stateBind;
            $projectInfo["comments"] = $commentsBind;
            $projectInfo["first_name"] = $salesFirstNameBind;
            $projectInfo["last_name"] = $salesLastNameBind;
            $recordSetActive[] = $projectInfo;
        }
        
        //close statement
        $stmt->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "getProjectNamesByStatus.php", $_GET['userID'], "prepare sql, active");
        
        $response["success"] = 0;
        $response["message"] = "Failed to get projects. Please contact admin.";
        die(json_encode($response));
    }
    
    
    //INACTIVE Status
    if($_GET['userType'] == 2) //Admin
    {
        $strSQL = "SELECT P.project_ID, P.project_name, P.project_latitude, P.project_longitude, S.status_description, P.date_opened, P.lot_area, P.power_cost_per_kWh, P.date_of_service, P.contact_name, P.contact_phone, P.contact_email, P.city, P.state, P.comments, U.first_name, U.last_name FROM Project P INNER JOIN Project_Status_Types S ON P.status = S.status_type_id INNER JOIN Users U ON P.user_ID = U.unique_user_id WHERE P.status = 2 ORDER BY P.date_opened DESC";
    }
    else //Sales Rep
    {
        $strSQL = "SELECT P.project_ID, P.project_name, P.project_latitude, P.project_longitude, S.status_description, P.date_opened, P.lot_area, P.power_cost_per_kWh, P.date_of_service, P.contact_name, P.contact_phone, P.contact_email, P.city, P.state, P.comments, U.first_name, U.last_name FROM Project P INNER JOIN Project_Status_Types S ON P.status = S.status_type_id INNER JOIN Users U ON P.user_ID = U.unique_user_id WHERE user_ID = ? AND P.status = 2 ORDER BY P.date_opened DESC";
    }
    $strSQLParams = $_GET['userID'];
    
    if($stmt = $mysqli->prepare($strSQL))
    {
        //bind parameters for markers
        if($_GET['userType'] == 3)//Sales Rep
        {
            $stmt->bind_param("s", $_GET['userID']);
        }
        
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "getProjectNamesByStatus.php", $_GET['userID'], "exec sql inactive");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to get projects. Please contact admin.";
            die(json_encode($response));
        }
        
        /* bind result variables */
        $stmt->bind_result($projectIDBind, $projectNameBind, $latitudeBind, $longitudeBind, $statusDescriptionBind, $dateOpenBind, $lotAreaBind, $powerCostBind, $dateOfServiceBind, $contactNameBind, $contactPhoneBind, $contactEmailBind, $cityBind, $stateBind, $commentsBind, $salesFirstNameBind, $salesLastNameBind);
        
        $recordSetInactive = array();
        while($row = $stmt->fetch())
        {
            $projectInfo["project_ID"] = $projectIDBind;
            $projectInfo["project_name"] = $projectNameBind;
            $projectInfo["project_latitude"] = $latitudeBind;
            $projectInfo["project_longitude"] = $longitudeBind;
            $projectInfo["status_description"] = $statusDescriptionBind;
            $projectInfo["date_opened"] = $dateOpenBind;
            $projectInfo["lot_area"] = $lotAreaBind;
            $projectInfo["power_cost_per_kWh"] = $powerCostBind;
            $projectInfo["date_of_service"] = $dateOfServiceBind;
            $projectInfo["contact_name"] = $contactNameBind;
            $projectInfo["contact_phone"] = $contactPhoneBind;
            $projectInfo["contact_email"] = $contactEmailBind;
            $projectInfo["city"] = $cityBind;
            $projectInfo["state"] = $stateBind;
            $projectInfo["comments"] = $commentsBind;
            $projectInfo["first_name"] = $salesFirstNameBind;
            $projectInfo["last_name"] = $salesLastNameBind;
            $recordSetInactive[] = $projectInfo;
        }
        
        //close statement
        $stmt->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "getProjectNamesByStatus.php", $_GET['userID'], "prepare sql, inactive");
        
        $response["success"] = 0;
        $response["message"] = "Failed to get projects. Please contact admin.";
        die(json_encode($response));
    }
    
    //CLOSED Status
    if($_GET['userType'] == 2) //Admin
    {
        $strSQL = "SELECT P.project_ID, P.project_name, P.project_latitude, P.project_longitude, S.status_description, P.date_opened, P.lot_area, P.power_cost_per_kWh, P.date_of_service, P.contact_name, P.contact_phone, P.contact_email, P.city, P.state, P.comments, U.first_name, U.last_name FROM Project P INNER JOIN Project_Status_Types S ON P.status = S.status_type_id INNER JOIN Users U ON P.user_ID = U.unique_user_id WHERE P.status = 3 ORDER BY P.date_opened DESC";
    }
    else //Sales Rep
    {
        $strSQL = "SELECT P.project_ID, P.project_name, P.project_latitude, P.project_longitude, S.status_description, P.date_opened, P.lot_area, P.power_cost_per_kWh, P.date_of_service, P.contact_name, P.contact_phone, P.contact_email, P.city, P.state, P.comments, U.first_name, U.last_name FROM Project P INNER JOIN Project_Status_Types S ON P.status = S.status_type_id INNER JOIN Users U ON P.user_ID = U.unique_user_id WHERE user_ID = ? AND P.status = 3 ORDER BY P.date_opened DESC";
    }
    $strSQLParams = $_GET['userID'];
    
    if($stmt = $mysqli->prepare($strSQL))
    {
        //bind parameters for markers
        if($_GET['userType'] == 3)//Sales Rep
        {
            $stmt->bind_param("s", $_GET['userID']);
        }
        
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "getProjectNamesByStatus.php", $_GET['userID'], "exec sql closed");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to get projects. Please contact admin.";
            die(json_encode($response));
        }
        
        /* bind result variables */
        $stmt->bind_result($projectIDBind, $projectNameBind, $latitudeBind, $longitudeBind, $statusDescriptionBind, $dateOpenBind, $lotAreaBind, $powerCostBind, $dateOfServiceBind, $contactNameBind, $contactPhoneBind, $contactEmailBind, $cityBind, $stateBind, $commentsBind, $salesFirstNameBind, $salesLastNameBind);
        
        $recordSetClosed = array();
        while($row = $stmt->fetch())
        {
            $projectInfo["project_ID"] = $projectIDBind;
            $projectInfo["project_name"] = $projectNameBind;
            $projectInfo["project_latitude"] = $latitudeBind;
            $projectInfo["project_longitude"] = $longitudeBind;
            $projectInfo["status_description"] = $statusDescriptionBind;
            $projectInfo["date_opened"] = $dateOpenBind;
            $projectInfo["lot_area"] = $lotAreaBind;
            $projectInfo["power_cost_per_kWh"] = $powerCostBind;
            $projectInfo["date_of_service"] = $dateOfServiceBind;
            $projectInfo["contact_name"] = $contactNameBind;
            $projectInfo["contact_phone"] = $contactPhoneBind;
            $projectInfo["contact_email"] = $contactEmailBind;
            $projectInfo["city"] = $cityBind;
            $projectInfo["state"] = $stateBind;
            $projectInfo["comments"] = $commentsBind;
            $projectInfo["first_name"] = $salesFirstNameBind;
            $projectInfo["last_name"] = $salesLastNameBind;
            $recordSetClosed[] = $projectInfo;
        }
        
        //close statement
        $stmt->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "getProjectNamesByStatus.php", $_GET['userID'], "prepare sql, closed");
        
        $response["success"] = 0;
        $response["message"] = "Failed to get projects. Please contact admin.";
        die(json_encode($response));
    }
    
    //close connection
    $mysqli->close();
    
    $response["success"] = 1;
    $response["active"] = $recordSetActive;
    $response["inactive"] = $recordSetInactive;
    $response["closed"] = $recordSetClosed;
    die(json_encode($response));
?>