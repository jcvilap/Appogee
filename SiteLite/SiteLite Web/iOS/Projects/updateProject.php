<?php
    header("Access-Control-Allow-Origin: *");
    
    //require("../connectionString.php");
    require("../../../inc/connectionString.php");
    
    $strSQL = "SELECT project_name, status, lot_area, power_cost_per_kWh, date_of_service, contact_name, contact_phone, contact_email, city, state, comments, project_latitude, project_longitude FROM Project WHERE project_ID = ?";
    $strSQLParams = $_POST['projectID'];
    
    if($stmt = $mysqli->prepare($strSQL))
    {
        //bind parameters for markers
        $stmt->bind_param("s", $_POST['projectID']);
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "updateProject.php", $_POST['userID'], "exec sql update, get info first");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to update project. Please contact admin.";
            die(json_encode($response));
        }
        
        /* bind result variables */
        $stmt->bind_result($projectNameBind, $statusBind, $lotAreaBind, $powerCostBind, $dateOfServiceBind, $contactNameBind, $contactPhoneBind, $contactEmailBind, $cityBind, $stateBind, $commentsBind, $latitudeBind, $longitudeBind);
        
        if($row = $stmt->fetch())
        {
            if($_POST['projectName'] != NULL)
            {
                $projectName = $_POST['projectName'];
            }
            else
            {
                $projectName = $projectNameBind;
            }
            
            if($_POST['status'] != NULL)
            {
                $status = $_POST['status'];
            }
            else
            {
                $status = $statusBind;
            }
            
            if($_POST['lotArea'] != NULL)
            {
                $lotArea = $_POST['lotArea'];
            }
            else
            {
                $lotArea = $lotAreaBind;
            }
            
            if($_POST['powerCost'] != NULL)
            {
                $powerCost = $_POST['powerCost'];
            }
            else
            {
                $powerCost = $powerCostBind;
            }
            
            if($_POST['dateOfService'] != NULL)
            {
                $dateOfService = $_POST['dateOfService'];
            }
            else
            {
                $dateOfService = $dateOfServiceBind;
            }
            
            if($_POST['contactName'] != NULL)
            {
                $contactName = $_POST['contactName'];
            }
            else
            {
                $contactName = $contactNameBind;
            }
            
            if($_POST['contactPhone'] != NULL)
            {
                $contactPhone = $_POST['contactPhone'];
            }
            else
            {
                $contactPhone = $contactPhoneBind;
            }
            
            if($_POST['contactEmail'] != NULL)
            {
                $contactEmail = $_POST['contactEmail'];
            }
            else
            {
                $contactEmail = $contactEmailBind;
            }
            
            if($_POST['city'] != NULL)
            {
                $city = $_POST['city'];
            }
            else
            {
                $city = $cityBind;
            }
            
            if($_POST['state'] != NULL)
            {
                $state = $_POST['state'];
            }
            else
            {
                $state = $stateBind;
            }
            
            if($_POST['comments'] != NULL)
            {
                $comments = $_POST['comments'];
            }
            else
            {
                $comments = $commentsBind;
            }
            
            if($_POST['projectLat'] != NULL)
            {
                $latitude = $_POST['projectLat'];
            }
            else
            {
                $latitude = $latitudeBind;
            }
            
            if($_POST['projectLong'] != NULL)
            {
                $longitude = $_POST['projectLong'];
            }
            else
            {
                $longitude = $longitudeBind;
            }
        }
        
        //close statement
        $stmt->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "updateProject.php", $_POST['userID'], "prepare sql, get info first");
        
        $response["success"] = 0;
        $response["message"] = "Failed to update project. Please contact admin.";
        die(json_encode($response));
    }
    
    
    $strSQL = "UPDATE Project SET project_name = ?, status = ?, lot_area = ?, power_cost_per_kWh = ?, date_of_service = ?, contact_name = ?, contact_phone = ?, contact_email = ?, city = ?, state = ?, comments = ?, project_latitude = ?, project_longitude = ? WHERE project_ID = ?";
    $strSQLParams = $projectName . ', ' . $status . ', ' . $lotArea . ', ' . $powerCost . ', ' . $dateOfService . ', ' . $contactName . ', ' . $contactPhone . ', ' . $contactEmail . ', ' . $city . ', ' . $state . ', ' . $comments . ', ' . $latitude . ', ' . $longitude . ', ' . $_POST['projectID'];
    
    if($stmt = $mysqli->prepare($strSQL))
    {
        //bind parameters for markers
        $stmt->bind_param("ssssssssssssss", $projectName, $status, $lotArea, $powerCost, $dateOfService, $contactName, $contactPhone, $contactEmail, $city, $state, $comments, $latitude, $longitude, $_POST['projectID']);
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "updateProject.php", $_POST['userID'], "exec sql update");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to update project. Please contact admin.";
            die(json_encode($response));
        }
        
        //close statement
        $stmt->close();
        //close connection
        $mysqli->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "updateProject.php", $_POST['userID'], "prepare sql statement update");
        
        $response["success"] = 0;
        $response["message"] = "Failed to update project. Please contact admin.";
        die(json_encode($response));
    }
    
    
    $response["success"] = 1;
    die(json_encode($response));
?>