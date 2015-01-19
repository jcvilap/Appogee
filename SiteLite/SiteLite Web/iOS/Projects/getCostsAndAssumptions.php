<?php
    
    require("../../../inc/connectionString.php");

    //Get Constants **********************************************
    $strSQL = "SELECT * FROM Costs_and_Assumptions";
    $strSQLParams = "none";
    if($result = $mysqli->query($strSQL))
    {
        
        /* fetch object array */
        if(!$costs = $result->fetch_assoc())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "getCostsAndAssumptions.php", "NA", "costs and assumptions, fetch_assoc");
            
            $response["success"] = 0;
            $response["message"] = "Failed to load projects. Please contact admin.";
            die(json_encode($response));
        }
        
        $response["emailSubject"] = $costs["email_subject_line"];
        $response["emailBeforeLink"] = $costs["email_before_link"];
        $response["emailAfterLink"] = $costs["email_after_link"];
        
        /* free result set */
        $result->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "getCostsAndAssumptions.php", $_GET['userID'], "exec sql, get costs and assumptions");
        
        $response["success"] = 0;
        $response["message"] = "Failed to load projects. Please contact admin.";
        die(json_encode($response));
    }
    //DONE Get Constants **********************************************
    
    //Get Name, Email, and Phone Number *******************************
    $strSQL = "SELECT first_name, last_name, email, phone_number FROM Users WHERE unique_user_id = ?";
    
    $strSQLParams = $_GET['userID'];
    if($stmt = $mysqli->prepare($strSQL))
    {
        //bind parameters
        $stmt->bind_param("s", $_GET['userID']);
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "getCostsAndAssumptions.php", $_GET['userID'], "exec sql, get user info by email");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to load projects. Please contact admin.";
            die(json_encode($response));
        }
        
        /* bind result variables */
        $stmt->bind_result($firstNameBind, $lastNameBind, $emailBind, $phoneNumberBind);
        
        //Enables 'num_rows'
        $stmt->store_result();
        
        $row = $stmt->fetch();
        
        $response["nameSalesPerson"] = $firstNameBind . " " . $lastNameBind;
        $response["emailSalesPerson"] = $emailBind;
        $response["phoneSalesPerson"] = $phoneNumberBind;
        
        //close statement
        $stmt->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "getCostsAndAssumptions.php", $_GET['userID'], "prepare sql, get user's info by email");
        
        $response["success"] = 0;
        $response["message"] = "Failed to load projects. Please contact admin. #3";
        die(json_encode($response));
    }
    //DONE Get Name, Email, and Phone Number *******************************
    
    
    
    $response["success"] = 1;
    die(json_encode($response));

?>