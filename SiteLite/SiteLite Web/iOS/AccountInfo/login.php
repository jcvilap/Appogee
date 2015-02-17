<?php
    
    header("Access-Control-Allow-Origin: *");
    
    session_start();
    
    require("../../../inc/connectionString.php");

    $strSQL = "SELECT unique_user_id, password, type, user_status, first_name, last_name, phone_number FROM Users WHERE email = ?";
    $strSQLParams = $_REQUEST['email'];
    if($stmt = $mysqli->prepare($strSQL))
    {
        //bind parameters
        $stmt->bind_param("s", $_REQUEST['email']);
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "login.php", "N/A", "exec sql, get user info by email");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to validate login credentials. Please contact admin.";
            die(json_encode($response));
        }
        
        /* bind result variables */
        $stmt->bind_result($userIDBind, $passwordBind, $typeBind, $statusIDBind, $firstNameBind, $lastNameBind, $phoneNumberBind);
        
        //Enables 'num_rows'
        $stmt->store_result();
        
        if($stmt->num_rows > 0)
        {
            if($row = $stmt->fetch())
            {
                if($statusIDBind == 1)
                {
                    $response["success"] = 0;
                    $response["message"] = "Your account is pending approval.";
                    die(json_encode($response));
                }
                //Active Account. Check Password
                else if($statusIDBind == 2)
                {
                    if($_REQUEST['password'] == $passwordBind)
                    {
                        //Set Session Variables
                        $_SESSION["userID"] = $userIDBind;
                        $_SESSION["userType"] = $typeBind;
                        
                        $response["success"] = 1;
                        $response["userID"] = $userIDBind;
                        $response["userType"] = $typeBind;
                        $response["firstName"] = $firstNameBind;
                        $response["lastName"] = $lastNameBind;
						$respones["repPhone"] = $phoneNumberBind;
                        die(json_encode($response));
                    }
                    //Incorrect Password
                    else
                    {
                        $response["success"] = 0;
                        $response["message"] = "Your password is incorrect.";
                        die(json_encode($response));
                    }
                }
                else if($statusIDBind == 3)
                {
                    $response["success"] = 0;
                    $response["message"] = "Your account has been rejected.";
                    die(json_encode($response));
                }
                //ERROR
                else
                {
                    $response["success"] = 0;
                    $response["message"] = "Unable to validate login credentials. Please contact admin.";
                    die(json_encode($response));
                }
            }
            
            $response["success"] = 0;
            $response["message"] = "The Email Address you entered is associated with an existing account.";
            die(json_encode($response));
        }
        //Email doesn't exist in Users table
        else
        {
            $response["success"] = 0;
            $response["message"] = "The Email Address you entered does not exist.";
            die(json_encode($response));
        }
        
        //close statement
        $stmt->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "login.php", "N/A", "prepare sql, get user's info by email");
        
        $response["success"] = 0;
        $response["message"] = "Failed to validate login credentials. Please contact admin.";
        die(json_encode($response));
    }

    
?>