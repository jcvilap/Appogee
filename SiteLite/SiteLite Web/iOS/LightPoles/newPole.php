<?php
    //require("../connectionString.php");
    require("../../../inc/connectionString.php");
    
    $strSQL = "INSERT INTO Pole(project_ID, marker_number, pole_latitude, pole_longitude, pole_exist, number_of_heads, bulb_ID, assembly_type_ID, legacy_wattage, hasPicture, one_to_one_replace, number_of_heads_proposed, pole_height, LED_fixture_ID, bracket) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $strSQLParams = $_POST['projectID'] . ', ' . $_POST['markerNum'] . ', ' . $_POST['poleLat'] . ', ' . $_POST['poleLong'] . ', ' . $_POST['poleExist'] . ', ' . $_POST['numHeads'] . ', ' . $_POST['bulbID'] . ', ' . $_POST['assemblyTypeID'] . ', ' . $_POST['legacyWattage'] . ', ' . $_POST['hasPicture'] . ', ' . $_POST['oneToOneReplace'] . ', ' . $_POST['numHeadsProposed'] . ', ' . $_POST['poleHeight'] . ', ' . $_POST['ledFixtureID'] . ', ' . $_POST['bracket'];
    
    if($stmt = $mysqli->prepare($strSQL))
    {
        //bind parameters for markers
        $stmt->bind_param("sssssssssssssss", $_POST['projectID'], $_POST['markerNum'], $_POST['poleLat'], $_POST['poleLong'], $_POST['poleExist'], $_POST['numHeads'], $_POST['bulbID'], $_POST['assemblyTypeID'], $_POST['legacyWattage'], $_POST['hasPicture'], $_POST['oneToOneReplace'], $_POST['numHeadsProposed'], $_POST['poleHeight'], $_POST['ledFixtureID'], $_POST['bracket']);
        
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "newPole.php", $_POST['userID'], "exec sql");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to create new pole. Please contact admin.";
            die(json_encode($response));
        }
        
        //close statement
        $stmt->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "newPole.php", $_POST['userID'], "prepare sql statement");
        
        $response["success"] = 0;
        $response["message"] = "Failed to create new pole. Please contact admin.";
        die(json_encode($response));
    }
    
    
    $response["success"] = 1;
    $response["message"] = strval($mysqli->insert_id);
    die(json_encode($response));
?>