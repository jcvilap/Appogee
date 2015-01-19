<?php
    
    require("../../../inc/connectionString.php");
    
    
    //Check to see if email address is valid**********************
    $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
    if (!preg_match($email_exp,$_POST['email']))
    {
        $response["success"] = 0;
        $response["message"] = "The Email Address you entered does not appear to be valid.";
        die(json_encode($response));
    }
    
    
    //Check if Email is already taken**********************
    $strSQL = "SELECT user_status FROM Users WHERE email = ?";
    $strSQLParams = $_POST['email'];
    if($stmt = $mysqli->prepare($strSQL))
    {
        //bind parameters
        $stmt->bind_param("s", $_POST['email']);
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "createNewUser.php", "N/A", "exec sql, check if email already exist");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to create new account. Please contact admin.";
            die(json_encode($response));
        }
        
        /* bind result variables */
        $stmt->bind_result($statusIDBind);
        
        //Enables 'num_rows'
        $stmt->store_result();
        
        if($stmt->num_rows > 0)
        {
            if($row = $stmt->fetch())
            {
                if($statusIDBind == 1)
                {
                    $response["success"] = 0;
                    $response["message"] = "The Email Address you entered is associated with an existing account that is pending approval.";
                    die(json_encode($response));
                }
                else if($statusIDBind == 2)
                {
                    $response["success"] = 0;
                    $response["message"] = "The Email Address you entered is associated with an active account.";
                    die(json_encode($response));
                }
                else if($statusIDBind == 3)
                {
                    $response["success"] = 0;
                    $response["message"] = "The Email Address you entered is associated with an account that was rejected.";
                    die(json_encode($response));
                }
                //ERROR
                else
                {
                    $response["success"] = 0;
                    $response["message"] = "The Email Address you entered is associated with an existing account. Please contact admin.";
                    die(json_encode($response));
                }
            }
            
            $response["success"] = 0;
            $response["message"] = "The Email Address you entered is associated with an existing account.";
            die(json_encode($response));
        }
        
        //close statement
        $stmt->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "createNewUser.php", "N/A", "prepare sql, check if email already exist");
        
        $response["success"] = 0;
        $response["message"] = "Failed to create new account. Please contact admin.";
        die(json_encode($response));
    }
    //DONE Checking if email is taken**********************
    
    
    //Create New Account***********************************
    $strSQL = "INSERT INTO Users(email, password, first_name, last_name) VALUES (?, ?, ?, ?)";
    $strSQLParams = $_POST['email'] . ', ' . $_POST['password'] . ', ' . $_POST['firstName'] . ', ' . $_POST['lastName'];
    
    if($stmt = $mysqli->prepare($strSQL))
    {
        //bind parameters for markers
        $stmt->bind_param("ssss", $_POST['email'], $_POST['password'], $_POST['firstName'], $_POST['lastName']);
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "createNewUser.php", "N/A", "exec sql, insert new user");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to create new account. Please contact admin.";
            die(json_encode($response));
        }
        
        //close statement
        $stmt->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "createNewUser.php", "N/A", "prepare sql statement, insert new user");
        
        $response["success"] = 0;
        $response["message"] = "Failed to create new account. Please contact admin.";
        die(json_encode($response));
    }
    
    
    $response["success"] = 1;
    $response["message"] = "Your new account has been submitted. After the Admin approves it, you will be able to log into the application.";
    die(json_encode($response));
    ?>