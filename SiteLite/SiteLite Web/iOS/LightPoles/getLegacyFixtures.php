<?php
    //require("../connectionString.php");
    require("../../../inc/connectionString.php");
    
    $recordSet = array();
    
    $strSQL = "SELECT * FROM Legacy_Fixture";
    
    //POST Bro Info
    if($result = $mysqli->query($strSQL))
    {
        while($row = $result->fetch_assoc())
        {
            $recordSet[] = $row;
        }
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:none', "getLegacyFixtures.php", "none", "basic select *");
    }
    
    //free result set
    $result->close();
    //close connection
    $mysqli->close();
    
    die(json_encode($recordSet));
?>