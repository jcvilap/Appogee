<?php
    //require("../connectionString.php");
    require("../../../inc/connectionString.php");
    
    $strSQL = "SELECT pole_latitude, pole_longitude, pole_exist, number_of_heads, bulb_ID, assembly_type_ID, legacy_wattage, hasPicture, one_to_one_replace, number_of_heads_proposed, pole_height, LED_fixture_ID, bracket FROM Pole WHERE pole_ID = ?";
    $strSQLParams = $_POST['poleID'];
    
    if($stmt = $mysqli->prepare($strSQL))
    {
        //bind parameters
        $stmt->bind_param("s", $_POST['poleID']);
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "updatePole.php", $_POST['userID'], "exec sql update, get info first");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to update pole. Please contact admin.";
            die(json_encode($response));
        }
        
        /* bind result variables */
        $stmt->bind_result($poleLatBind, $poleLongBind, $poleExistgBind, $numHeadsBind, $bulbIDBind, $assemblyTypeIDBind, $legacyWattageBind, $hasPictureBind, $oneToOneReplaceBind, $numHeadsProposedBind, $poleHeightBind, $ledFixtureIDBind, $bracketBind);
        
        if($row = $stmt->fetch())
        {
            if($_POST['poleLat'] != NULL)
            {
                $poleLat = $_POST['poleLat'];
            }
            else
            {
                $poleLat = $poleLatBind;
            }
            
            if($_POST['poleLong'] != NULL)
            {
                $poleLong = $_POST['poleLong'];
            }
            else
            {
                $poleLong = $poleLongBind;
            }
            
            if($_POST['poleExist'] != NULL)
            {
                $poleExist = $_POST['poleExist'];
            }
            else
            {
                $poleExist = $poleExistgBind;
            }
            
            if($_POST['numHeads'] != NULL)
            {
                $numHeads = $_POST['numHeads'];
            }
            else
            {
                $numHeads = $numHeadsBind;
            }
            
            if($_POST['bulbID'] != NULL)
            {
                $bulbID = $_POST['bulbID'];
            }
            else
            {
                $bulbID = $bulbIDBind;
            }
            
            if($_POST['assemblyTypeID'] != NULL)
            {
                $assemblyTypeID = $_POST['assemblyTypeID'];
            }
            else
            {
                $assemblyTypeID = $assemblyTypeIDBind;
            }
            
            if($_POST['legacyWattage'] != NULL)
            {
                $legacyWattage = $_POST['legacyWattage'];
            }
            else
            {
                $legacyWattage = $legacyWattageBind;
            }
            
            if($_POST['hasPicture'] != NULL)
            {
                $hasPicture = $_POST['hasPicture'];
            }
            else
            {
                $hasPicture = $hasPictureBind;
            }
            
            if($_POST['oneToOneReplace'] != NULL)
            {
                $oneToOneReplace = $_POST['oneToOneReplace'];
            }
            else
            {
                $oneToOneReplace = $oneToOneReplaceBind;
            }
            
            if($_POST['numHeadsProposed'] != NULL)
            {
                $numHeadsProposed = $_POST['numHeadsProposed'];
            }
            else
            {
                $numHeadsProposed = $numHeadsProposedBind;
            }
            
            if($_POST['poleHeight'] != NULL)
            {
                $poleHeight = $_POST['poleHeight'];
            }
            else
            {
                $poleHeight = $poleHeightBind;
            }
            
            if($_POST['ledFixtureID'] != NULL)
            {
                $ledFixtureID = $_POST['ledFixtureID'];
            }
            else
            {
                $ledFixtureID = $ledFixtureIDBind;
            }
            
            if($_POST['bracket'] != NULL)
            {
                $bracket = $_POST['bracket'];
            }
            else
            {
                $bracket = $bracketBind;
            }
            
            /*
            if($_POST['picture'] != NULL)
            {
                if($_POST['picture'] == "none")
                {
                    $picture = "";
                }
                else
                {
                    $picture = $_POST['picture'];
                }
            } */
            
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
    
    /*if($_POST['picture'] == NULL)
    {
        $strSQL = "UPDATE Pole SET pole_latitude = ?, pole_longitude = ?, pole_exist = ?, number_of_heads = ?, bulb_ID = ?, assembly_type_ID = ?, legacy_wattage = ?, one_to_one_replace = ?, number_of_heads_proposed = ?, pole_height = ?, LED_fixture_ID = ?, bracket = ? WHERE pole_ID = ?";
        $strSQLParams = $poleLat . ', ' . $poleLong . ', ' . $poleExist . ', ' . $numHeads . ', ' . $bulbID . ', ' . $assemblyTypeIDBind . ', ' . $legacyWattage . ', ' . $oneToOneReplace . ', ' . $numHeadsProposed . ', ' . $poleHeight . ', ' . $ledFixtureID . ', ' . $bracket . ', ' . $_POST['poleID'];
    }
    else
    {*/
    
    $strSQL = "UPDATE Pole SET pole_latitude = ?, pole_longitude = ?, pole_exist = ?, number_of_heads = ?, bulb_ID = ?, assembly_type_ID = ?, legacy_wattage = ?, hasPicture = ?, one_to_one_replace = ?, number_of_heads_proposed = ?, pole_height = ?, LED_fixture_ID = ?, bracket = ? WHERE pole_ID = ?";
    $strSQLParams = $poleLat . ', ' . $poleLong . ', ' . $poleExist . ', ' . $numHeads . ', ' . $bulbID . ', ' . $assemblyTypeIDBind . ', ' . $legacyWattage . ', ' . $hasPicture . ', ' . $oneToOneReplace . ', ' . $numHeadsProposed . ', ' . $poleHeight . ', ' . $ledFixtureID . ', ' . $bracket . ', ' . $_POST['poleID'];
    //}
    
    if($stmt = $mysqli->prepare($strSQL))
    {
        //bind parameters for markers
        /*if($_POST['picture'] == NULL)
        {
            $stmt->bind_param("sssssssssssss", $poleLat, $poleLong, $poleExist, $numHeads, $bulbID, $assemblyTypeID, $legacyWattage, $oneToOneReplace, $numHeadsProposed, $poleHeight, $ledFixtureID, $bracket, $_POST['poleID']);
        }
        else
        {*/
        $stmt->bind_param("ssssssssssssss", $poleLat, $poleLong, $poleExist, $numHeads, $bulbID, $assemblyTypeID, $legacyWattage, $hasPicture, $oneToOneReplace, $numHeadsProposed, $poleHeight, $ledFixtureID, $bracket, $_POST['poleID']);
        //}
        
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "updatePole.php", $_POST['userID'], "exec sql update");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to update pole. Please contact admin.";
            die(json_encode($response));
        }
        
        //close statement
        $stmt->close();
        //close connection
        $mysqli->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "updatePole.php", $_POST['userID'], "prepare sql statement update");
        
        $response["success"] = 0;
        $response["message"] = "Failed to update project. Please contact admin.";
        die(json_encode($response));
    }
    
    
    $response["success"] = 1;
    die(json_encode($response));
?>