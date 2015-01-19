<?php
    header("Access-Control-Allow-Origin: *");
    
    //require("../connectionString.php");
    require("../../../inc/connectionString.php");
    
    $strSQL = "SELECT P.pole_ID, P.marker_number, P.pole_latitude, P.pole_longitude, P.pole_exist, P.number_of_heads, P.bulb_ID, Leg.bulb_description, P.assembly_type_ID, P.legacy_wattage, P.hasPicture, P.one_to_one_replace, P.number_of_heads_proposed, P.pole_height, P.LED_fixture_ID, LED.part_number, LED.LED_fixture_description, LED.LED_wattage, LED.unit_cost, P.bracket FROM Pole P INNER JOIN Legacy_Fixture Leg ON P.bulb_ID = Leg.bulb_ID INNER JOIN LED_Fixture LED ON P.LED_fixture_ID = LED.LED_fixture_ID WHERE project_ID = ? ORDER BY P.marker_number";
    $strSQLParams = $_GET['projectID'];
    
    if($stmt = $mysqli->prepare($strSQL))
    {
        //bind parameters for markers
        $stmt->bind_param("s", $_GET['projectID']);
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "getPoles.php", $_GET['userID'], "exec sql");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to get light poles. Please contact admin.";
            die(json_encode($response));
        }
        
        /* bind result variables */
        $stmt->bind_result($poleIDBind, $markerNumBind, $poleLatBind, $poleLongBind, $poleExistBind, $numOfHeadsBind, $bulbIDBind, $bulbDescBind, $assemblyTypeIDBind, $legWattageBind, $hasPictureBind, $oneToOneBind, $numOfHeadsProposedBind, $poleHeightBind, $LEDfixtureIDBind, $LEDpartNumberBind, $LEDdescBind, $LEDwattageBind, $LEDunitCostBind, $bracketBind);
        
        $recordSet = array();
        while($row = $stmt->fetch())
        {
            $poleInfo["poleID"] = $poleIDBind;
            $poleInfo["markerNum"] = $markerNumBind;
            $poleInfo["poleLat"] = $poleLatBind;
            $poleInfo["poleLong"] = $poleLongBind;
            $poleInfo["poleExist"] = $poleExistBind;
            $poleInfo["numOfHeads"] = $numOfHeadsBind;
            $poleInfo["bulbID"] = $bulbIDBind;
            $poleInfo["bulbDesc"] = $bulbDescBind;
            $poleInfo["assemblyTypeID"] = $assemblyTypeIDBind;
            $poleInfo["legWattage"] = $legWattageBind;
            $poleInfo["hasPicture"] = $hasPictureBind;
            $poleInfo["oneToOne"] = $oneToOneBind;
            $poleInfo["numOfHeadsProposed"] = $numOfHeadsProposedBind;
            $poleInfo["poleHeight"] = $poleHeightBind;
            $poleInfo["LEDfixtureID"] = $LEDfixtureIDBind;
            $poleInfo["LEDpartNumber"] = $LEDpartNumberBind;
            $poleInfo["LEDdesc"] = $LEDdescBind;
            $poleInfo["LEDwattage"] = $LEDwattageBind;
            $poleInfo["LEDunitCost"] = $LEDunitCostBind;
            $poleInfo["bracket"] = $bracketBind;
            $recordSet[] = $poleInfo;
        }
        
        //close statement
        $stmt->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "getPoles.php", $_GET['userID'], "prepare sql");
        
        $response["success"] = 0;
        $response["message"] = "Failed to get light poles. Please contact admin.";
        die(json_encode($response));
    }
    
    
    //close connection
    $mysqli->close();
    
    $response["success"] = 1;
    $response["message"] = $recordSet;
    die(json_encode($response));
?>