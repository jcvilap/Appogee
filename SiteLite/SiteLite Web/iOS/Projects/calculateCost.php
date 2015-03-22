<?php
    header("Access-Control-Allow-Origin: *");
    
    //require("../connectionString.php");
    require("../../../inc/connectionString.php");
    
    //Get Costs and Assumptions CONSTANTS *******************************************************************************
    $strSQL = "SELECT * FROM Costs_and_Assumptions";
    $strSQLParams = "none";
    if($result = $mysqli->query($strSQL))
    {
        
        /* fetch object array */
        if(!$costs = $result->fetch_assoc())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "calculateCost.php", "NA", "costs and assumptions, fetch_assoc");
            
            $response["success"] = 0;
            $response["message"] = "Failed to calculate cost of project. Please contact admin.";
            die(json_encode($response));
        }
        
        /* free result set */
        $result->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "calculateCost.php", "NA", "exec sql, get costs and assumptions");
        
        $response["success"] = 0;
        $response["message"] = "Failed to calculate cost of project. Please contact admin.";
        die(json_encode($response));
    }
    //Get Costs and Assumptions Constants END****************************************************************************
    
    
    //Get Project Info **************************************************************************************************
    $strSQL = "SELECT PR.lot_area, PR.power_cost_per_kWH, PR.date_of_service, PR.state FROM Project PR WHERE PR.project_ID = ?";
    
    $strSQLParams = $_GET['projectID'];
    
    
    if($stmt = $mysqli->prepare($strSQL))
    {
        $stmt->bind_param("s", $_GET['projectID']);
        
        
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "calculateCost.php", "NA", "exec sql get Project info");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to calculate cost of project. Please contact admin.";
            die(json_encode($response));
        }
        
        /* bind result variables */
        $stmt->bind_result($lotAreaBind, $powerCostPerKWHBind, $dateOfServiceBind, $stateBind);
        
        if(!$projectInfo = $stmt->fetch())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "calculateCost.php", "NA", "get Project info, stmt-fetch()");
            
            $response["success"] = 0;
            $response["message"] = "Failed to calculate cost of project. Please contact admin.";
            die(json_encode($response));
        }
        
        //close statement
        $stmt->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "calculateCost.php", "NA", "prepare sql, get Project info");
        
        $response["success"] = 0;
        $response["message"] = "Failed to calculate cost of project. Please contact admin.";
        die(json_encode($response));
    }
    //Get Project Info END **********************************************************************************************
    
    
    //Light Pole Markers ************************************************************************************************
    $strSQL = "SELECT P.pole_exist, P.number_of_heads, P.assembly_type_ID, P.legacy_wattage, P.one_to_one_replace, P.number_of_heads_proposed, P.bracket, L.LED_wattage, L.unit_cost, L.is_wallpack FROM Pole P LEFT OUTER JOIN LED_Fixture L ON P.LED_fixture_ID = L.LED_fixture_ID WHERE P.project_ID = ?";
    
    $strSQLParams = $_GET['projectID'];
    
    
    if($stmt = $mysqli->prepare($strSQL))
    {
        $stmt->bind_param("s", $_GET['projectID']);
        
        
        //execute query
        if(!$stmt->execute())
        {
            insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "calculateCost.php", "NA", "exec sql get Poles info");
            
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "Failed to calculate cost of project. Please contact admin.";
            die(json_encode($response));
        }
        
        //Enables 'num_rows'
        $stmt->store_result();
        
        /* bind result variables */
        $stmt->bind_result($poleExistBind, $numOfHeadsExistingBind, $assemblytypeIDBind, $legacyWattageBind, $oneToOneReplaceBind, $numOfHeadsProposedBind, $bracketBind, $ledWattageProposedBind, $unitCostProposedBind, $proposedIsWallpack);
        
        if($stmt->num_rows == 0) //No markers exisit. Return ERROR
        {
            //close statement
            $stmt->close();
            
            $response["success"] = 0;
            $response["message"] = "No Light Pole Markers exist.";
            die(json_encode($response));
        }
        
        $sumCostProposedLED = 0; //Product Cost Standard/Expedited
        $sumWallPackProposed = 0; //Installation Cost
        $sumShoeBoxProposedWithBracket = 0; //Installation Cost
        $sumShoeBoxProposedNoBracket = 0; //Installation Cost
        $sumExistingMaintenanceCost = 0; //Maintenance Cost for Legacy Bulbs
        $sumLegacyWattage = 0; //Existing Year-by-Year Power Cost
        $sumLEDWattage = 0; //Existing Year-by-Year Power Cost
        
        while($row = $stmt->fetch())
        {
            if($oneToOneReplaceBind == 1)
            {
                $sumCostProposedLED = $sumCostProposedLED + ($numOfHeadsProposedBind * $unitCostProposedBind);
                $sumLEDWattage = $sumLEDWattage + ($numOfHeadsExistingBind * $ledWattageProposedBind);
                
                //Wallpack for Installation Cost
                if($proposedIsWallpack == 1)
                {
                    $sumWallPackProposed = $sumWallPackProposed + ($numOfHeadsProposedBind * $costs["wallpack_install_cost"]);
                }
                //Shoebox for Installation Cost
                else
                {
                    //With Bracket
                    if($bracketBind == 1)
                    {
                        $sumShoeBoxProposedWithBracket = $sumShoeBoxProposedWithBracket + ($numOfHeadsProposedBind * $costs["bracket_install_cost"]);

                    }
                    //No Bracket
                    else
                    {
                        $sumShoeBoxProposedNoBracket = $sumShoeBoxProposedNoBracket + ($numOfHeadsProposedBind * $costs["installation_cost"]);
                    }
                }
            }
            
            if($poleExistBind == 1) //Existing Maintenance Cost for Legacy Bulbs
            {
                $sumLegacyWattage = $sumLegacyWattage + ($numOfHeadsExistingBind * $legacyWattageBind);
                
                //Wallpack
                if($assemblytypeIDBind == 1)
                {
                    $sumExistingMaintenanceCost = $sumExistingMaintenanceCost + ($numOfHeadsExistingBind * $costs["wallpack_maintenance_cost"]);
                }
                //Shoebox
                else
                {
                    $sumExistingMaintenanceCost = $sumExistingMaintenanceCost + ($numOfHeadsExistingBind * $costs["shoebox_maintenance_cost"]);
                }
            }
        }
		
					

        
        //close statement
        $stmt->close();
    }
    else
    {
        insertErrorTable($strSQL . ' PRM:' . $strSQLParams, "calculateCost.php", "NA", "prepare sql, get poles info");
        
        $response["success"] = 0;
        $response["message"] = "Failed to calculate cost of project. Please contact admin.";
        die(json_encode($response));
    }
    
	$response['totalLEDwattage'] = $sumLEDWattage;
    $response["productCostStandard"] = $sumCostProposedLED * $costs["standard_shipping"] * $costs["markup"];
    $response["productCostExpedited"] = $sumCostProposedLED * $costs["standard_shipping"] * $costs["markup"] * $costs["expedited_shipping"];
    $response["installationCost"] = $sumWallPackProposed + $sumShoeBoxProposedWithBracket + $sumShoeBoxProposedNoBracket;
    $response["existingYearlyMaintenanceCost"] = $sumExistingMaintenanceCost / $costs["legacy_lifespan"];
    //Light Pole Markers END *******************************************************************************

	//Shipping Only
    $response["standardShippingOnly"] = $response["productCostStandard"] - $sumCostProposedLED;
	$response["expeditedShippingOnly"] = $response["productCostExpedited"] - $sumCostProposedLED;
    
    //push markup through
	$response["markup"] = $costs["markup"];

	//push sales through
	$response["shipping"] =$costs["standard_shipping"];
    
    //Sales Tax ********************************************************************************************
    //Standard Shipping
    $salesTaxStandard = 0;
    if($stateBind == "FL")
    {
        if(($response["productCostStandard"] + $response["installationCost"]) < $costs["tax_rate_cutoff"])
        {
            $salesTaxStandard = $costs["low_tax_rate"] * $response["productCostStandard"];
        }
        else
        {
            $salesTaxStandard = $costs["county_sur_tax"] + $costs["high_tax_rate"] * ($response["productCostStandard"] + $costs["tax_rate_cutoff"]);
        }
    }
    else
    {
        $salesTaxStandard = 0;
    }
    
    
    //Expedited Shipping
    $salesTaxExpedited = 0;
    if($stateBind == "FL")
    {
        if(($response["productCostExpedited"] + $response["installationCost"]) < $costs["tax_rate_cutoff"])
        {
            $salesTaxExpedited = $costs["low_tax_rate"] * $response["productCostExpedited"];
        }
        else
        {
            $salesTaxExpedited = $costs["county_sur_tax"] + $costs["high_tax_rate"] * ($response["productCostExpedited"] - $costs["tax_rate_cutoff"]);
        }
    }
    else
    {
        $salesTaxExpedited = 0;
    }
    
    
    $response["salesTaxStandard"] = $salesTaxStandard;
    $response["salesTaxExpedited"] = $salesTaxExpedited;
    //Sales Tax END *****************************************************************************************
    
    //Total Cost ********************************************************************************************
    $response["totalCostStandard"] = $response["productCostStandard"] + $response["installationCost"] + $response["salesTaxStandard"];
    $response["totalCostExpedited"] = $response["productCostExpedited"] + $response["installationCost"] + $response["salesTaxExpedited"];
    //Total Cost END ****************************************************************************************

    //Total Lease Cost***************************************************************************************
    //Standard
    $response["totalLeaseCostStandard"] = $costs["financing_cost"] * $response["totalCostStandard"];
    $response["monthlyLeasePaymentStandard"] = $response["totalLeaseCostStandard"] / $costs["lease_term"];
    $response["yearlyLeasePaymentStandard"] = $response["monthlyLeasePaymentStandard"] * 12;
    //Expedited
    $response["totalLeaseCostExpedited"] = $costs["financing_cost"] * $response["totalCostExpedited"];
    $response["monthlyLeasePaymentExpedited"] = $response["totalLeaseCostExpedited"] / $costs["lease_term"];
    $response["yearlyLeasePaymentExpedited"] = $response["monthlyLeasePaymentExpedited"] * 12;
    //Total Lease Cost END **********************************************************************************
    
    //Existing Year-by-Year Power Cost **********************************************************************
    $arrayExistingYearCost = array();
    for($year = 0; $year <= 9; $year++)
    {
        $yearCost = $sumLegacyWattage * ((($powerCostPerKWHBind/100 + ($costs["power_cost_increase"] * ($year))) * $costs["usage_hours"]) / 1000);
        $arrayExistingYearCost[$year] = number_format($yearCost, 2, '.', '');
    }
    $response["existingYearByYearPowerCost"] = $arrayExistingYearCost;
    //Existing Year-by-Year Power Cost END ******************************************************************
    
    //Proposed Year-by-Year Power Cost **********************************************************************
    $arrayProposedYearCost = array();
    for($year = 0; $year <= 9; $year++)
    {
        $yearCost = $sumLEDWattage * ((($powerCostPerKWHBind/100 + ($costs["power_cost_increase"] * $year))  * $costs["usage_hours"]) / 1000);
        $arrayProposedYearCost[$year] = number_format($yearCost, 2, '.', '');
    }
    $response["proposedYearByYearPowerCost"] = $arrayProposedYearCost;
    //Proposed Year-by-Year Power Cost END ******************************************************************
    
    //Tax Abandonment ***************************************************************************************
    $dateDifference = date("Y") - $dateOfServiceBind;
    $taxAbandonment = $costs["abandonment_multiplier"] * (1 - $dateDifference / 39) * $sumExistingMaintenanceCost;
    if($taxAbandonment < 2500)
    {
        $taxAbandonment = 0;
    }
    $response["taxAbandonment"] = number_format($taxAbandonment, 2, '.', '');
    //Tax Abandonment END ***********************************************************************************
    
    //Year-By-Year Savings **********************************************************************************
    $arrayYearByYearSavings = array();
    for($year = 0; $year <= 9; $year++)
    {
        if($year == 0)
        {
            $yearCost = $response["existingYearlyMaintenanceCost"] + $arrayExistingYearCost[$year] + $response["taxAbandonment"] - $arrayProposedYearCost[$year];
        }
        else
        {
            $yearCost = $response["existingYearlyMaintenanceCost"] + $arrayExistingYearCost[$year] - $arrayProposedYearCost[$year];
        }
        
        $arrayYearByYearSavings[$year] = number_format($yearCost, 2, '.', '');
    }
    $response["yearByYearSavings"] = $arrayYearByYearSavings;
    //Year-By-Year Savings END **********************************************************************************
    

    //Existing Monthly kg Coal Usage ****************************************************************************
    
   $response["existingKgCoal"] = $sumLegacyWattage * $costs["usage_hours"] * 0.0001475 / 12 * 2.2; 

    //Existing Monthly kg Coal Usage END ************************************************************************
    
    //Proposed Monthly kg Coal Usage ****************************************************************************
    
   $response["proposedKgCoal"] = $sumLEDWattage * $costs["usage_hours"] * 0.0001475 / 12 * 2.2; 

    //Proposed Monthly kg Coal Usage END ************************************************************************

   
  
    //Simple Payback Period Standard ****************************************************************************

    $exceededCost = false;

    $sumSavings = 0;

    $year = 0;
	
    while ( $exceededCost == false && $year <= 9)
    {
         if ($sumSavings < $response["totalCostStandard"])
        {
            $sumSavings += $arrayYearByYearSavings[$year];
            $year++;
			
        }
        
        else 
        {
           $response["simplePaybackPeriodStandard"]= (((($response["totalCostStandard"]-$sumSavings)/$arrayYearByYearSavings[$year])+$year)*12);
            $exceededCost = true;
			
        }
    }
    
    //Simple Payback Period Standard  END ************************************************************************
                     

    //Simple Payback Period Expedited ****************************************************************************
    
   
    $exceededCost = false;

    $sumSavings = 0;

    $year = 0;
	
    while ( $exceededCost == false && $year <= 9)
    {
         if ($sumSavings < $response["totalCostExpedited"])
        {
            $sumSavings += $arrayYearByYearSavings[$year];
            $year++;
			
        }
        
        else 
        {
           $response["simplePaybackPeriodExpedited"]= (((($response["totalCostExpedited"]-$sumSavings)/$arrayYearByYearSavings[$year])+$year)*12);
            $exceededCost = true;
			
        }
    }
    
    //Simple Payback Period Expedited  END ************************************************************************
                     
             
        


    
    //Format Currency to 2 decimal places
    $response["productCostStandard"] = number_format($response["productCostStandard"], 2, '.', '');
    $response["productCostExpedited"] = number_format($response["productCostExpedited"], 2, '.', '');
    $response["installationCost"] = number_format($response["installationCost"], 2, '.', '');
    $response["existingYearlyMaintenanceCost"] = number_format($response["existingYearlyMaintenanceCost"], 2, '.', '');
    $response["salesTaxStandard"] = number_format($response["salesTaxStandard"], 2, '.', '');
    $response["salesTaxExpedited"] = number_format($response["salesTaxExpedited"], 2, '.', '');
	$response["standardShippingOnly"] = number_format($response["standardShippingOnly"], 2, '.', '');
    $response["expeditedShippingOnly"] = number_format($response["expeditedShippingOnly"], 2, '.', '');
    $response["totalCostStandard"] = number_format($response["totalCostStandard"], 2, '.', '');
    $response["totalCostExpedited"] = number_format($response["totalCostExpedited"], 2, '.', '');
    $response["totalLeaseCostStandard"] = number_format($response["totalLeaseCostStandard"], 2, '.', '');
    $response["monthlyLeasePaymentStandard"] = number_format($response["monthlyLeasePaymentStandard"], 2, '.', '');
    $response["yearlyLeasePaymentStandard"] = number_format($response["yearlyLeasePaymentStandard"], 2, '.', '');
    $response["totalLeaseCostExpedited"] = number_format($response["totalLeaseCostExpedited"], 2, '.', '');
    $response["monthlyLeasePaymentExpedited"] = number_format($response["monthlyLeasePaymentExpedited"], 2, '.', '');
    $response["yearlyLeasePaymentExpedited"] = number_format($response["yearlyLeasePaymentExpedited"], 2, '.', '');
    $response["existingKgCoal"] = number_format($response["existingKgCoal"], 0, '.', '');
    $response["proposedKgCoal"] = number_format($response["proposedKgCoal"], 0, '.', '');
    $response["simplePaybackPeriodStandard"] = number_format($response["simplePaybackPeriodStandard"], 0, '.', '');
    $response["simplePaybackPeriodExpedited"] = number_format($response["simplePaybackPeriodExpedited"], 0, '.', '');
	$response["totalLEDwattage"] = number_format($response["totalLEDwattage"], 0, '.', '');
   
    
    $response["success"] = 1;
    die(json_encode($response));
?>