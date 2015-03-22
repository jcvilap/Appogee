arkonLEDApp.controller('PrintController',function ($scope, $http, $routeParams, projectsFactory, commonFactory){
    $scope.params = $routeParams;
    $scope.activeView = $scope.params.activeView;
    $scope.paymentType = $scope.params.paymentType;
    var calculationsData = null;
    $scope.projects = [];
    $scope.activeProject = null;
    $scope.areaChartCreated = false;
    $scope.baseUrl = commonFactory.baseUrl;
    $scope.savingsMethod = $scope.params.savingsMethod;
    $scope.existingMonthlyOperationalCost = null;
    $scope.proposedMonthlyOperationalCost = null;
    $scope.toFormattedNumber = commonFactory.toFormattedNumber;

    $scope.changeProposedOperationalCostChart = function(savingsMethod, activeView){
        if (savingsMethod === 'immediate') {
            // Change bottomText dynamically
            $scope.proposedOperationalCostChart.axes[0].setBottomText($scope.proposedMonthlyOperationalCost = commonFactory.toFormattedNumber(Number(calculationsData.proposedYearByYearPowerCost[0])/12 + Number(activeView == 'standardShipping'? calculationsData.monthlyLeasePaymentStandard: calculationsData.monthlyLeasePaymentExpedited)));
            // Change arrow dynamically
            $scope.proposedOperationalCostChart.arrows[0].setValue(Number(calculationsData.proposedYearByYearPowerCost[0])/12 + Number(activeView == 'standardShipping'? calculationsData.monthlyLeasePaymentStandard: calculationsData.monthlyLeasePaymentExpedited));
        } else {
            // Change bottomText dynamically
            $scope.proposedOperationalCostChart.axes[0].setBottomText($scope.proposedMonthlyOperationalCost = commonFactory.toFormattedNumber(Number(calculationsData.proposedYearByYearPowerCost[0])/12));
            // Change arrow dynamically
            $scope.proposedOperationalCostChart.arrows[0].setValue(Number(calculationsData.proposedYearByYearPowerCost[0])/12);
        }
    }

    // Get Project by ID
    $scope.getProject = function(id){
        for (var i = $scope.projects.length - 1; i >= 0; i--) {
            if($scope.projects[i].project_ID == id)
                return $scope.projects[i];
        };
    };

    // Load Details Pane and scroll to it
    $scope.loadDetails = function(project){
        // Update active project
        $scope.activeProject = project;
        // Get call for projects poles
        projectsFactory.getProjectPoles(project.project_ID, function(data){
            // Update project list
            $scope.activeProject.poles = data;
            $scope.activeProject.lightFixtureTablePoles = getLightFixturesPoles();

            var mapUrl = 'https://maps.googleapis.com/maps/api/staticmap?size=1200x800&scale=2&maptype=hybrid&markers=size:small|';
            for( var i = 0 ; i < data.length - 1 ; i ++){
                if(mapUrl.length < 2020){
                    mapUrl += data[i].poleLat + ',' + data[i].poleLong + '|';
                }
                else {
                    break;
                }
            }
            mapUrl += data[data.length - 1].poleLat + ',' + data[data.length - 1].poleLong;
            mapUrl += '&zoom=' + $scope.params.mapZoom;
            $scope.mapUrl = mapUrl;
        });

        // Update projects stats
        projectsFactory.getProjectStats(project.project_ID, function(data){
            // Clone original data to avoid changes to the original source
            calculationsData = $.extend(true, {}, data);

            $scope.activeProject.calculationsData = data;
            commonFactory.updateScope($scope);
            $scope.activeProject.stats = commonFactory.calculateChartPoints(calculationsData);
            // TODO:Check with Arkon this field
            $scope.activeProject.totalSavings = calculateTotalSavings(data);

            // Assign value for UI use
            $scope.activeProject.calculationsData.existingMonthlyPowerCost = commonFactory.toFormattedNumber(Number(data.existingYearByYearPowerCost[0])/12);

            $scope.activeProject.calculationsData.proposedMonthlyPowerCost = commonFactory.toFormattedNumber(Number(data.proposedYearByYearPowerCost[0])/12);

            $scope.activeProject.calculationsData.existingMonthlyPowerUsage = commonFactory.toFormattedNumber(((Number(data.existingYearByYearPowerCost[0])/12).toFixed(2)/Number($scope.activeProject.power_cost_per_kWh)));

            $scope.activeProject.calculationsData.proposedMonthlyPowerUsage = commonFactory.toFormattedNumber(((Number(data.proposedYearByYearPowerCost[0])/12)/Number($scope.activeProject.power_cost_per_kWh)));

            $scope.activeProject.calculationsData.powerSavings = commonFactory.toFormattedNumber(Number(data.existingYearByYearPowerCost[0]) - Number(data.proposedYearByYearPowerCost[0]));

            $scope.activeProject.calculationsData.totalPowerSavings = commonFactory.toFormattedNumber((Number(data.existingYearByYearPowerCost[5]) - Number(data.proposedYearByYearPowerCost[4])) * 10);

            $scope.activeProject.calculationsData.immediateMonthlySavingsExpedited = commonFactory.toFormattedNumber(
                Number(data.existingYearlyMaintenanceCost)/12 +
                Number(data.existingYearByYearPowerCost[0])/12 -
                Number(data.monthlyLeasePaymentExpedited) - Number(data.proposedYearByYearPowerCost[0])/12
            );
            $scope.activeProject.calculationsData.immediateMonthlySavingsStandard = commonFactory.toFormattedNumber(
                Number(data.existingYearlyMaintenanceCost)/12 +
                Number(data.existingYearByYearPowerCost[0])/12 -
                Number(data.monthlyLeasePaymentStandard) - Number(data.proposedYearByYearPowerCost[0])/12
            );
            $scope.activeProject.calculationsData.monthlyLeasePaymentStandard = commonFactory.toFormattedNumber(data.monthlyLeasePaymentStandard);
            $scope.activeProject.calculationsData.monthlyLeasePaymentExpedited = commonFactory.toFormattedNumber(data.monthlyLeasePaymentExpedited);
            $scope.activeProject.calculationsData.existingMonthlyMaintenanceCost = commonFactory.toFormattedNumber(Number(data.existingYearlyMaintenanceCost)/12);
            $scope.activeProject.calculationsData.existingYearlyMaintenanceCost = commonFactory.toFormattedNumber(Number(data.existingYearlyMaintenanceCost));

            $scope.activeProject.calculationsData.existingTotalMaintenanceCost = commonFactory.toFormattedNumber(Number((data.existingYearlyMaintenanceCost)*10));

            $scope.activeProject.calculationsData.operationalSavingsLongTerm = commonFactory.toFormattedNumber(
                Number(calculationsData.existingYearlyMaintenanceCost)/12 +
                Number(calculationsData.existingYearByYearPowerCost[0])/12 - Number(data.proposedYearByYearPowerCost[0])/12
            );

            $scope.areaChart = Morris.Area({
                element: 'morris-area-chart',
                data: $scope.activeProject.stats ,
                xkey: 'year',
                ykeys: ['existingMantenanceCost', 'existingPowerCost', 'LEDLeasePayment', 'proposedPowerCost'],
                labels: ['Maintenance Savings', 'Power Savings', 'LED Finance Payments', 'LED Power Costs'],
                lineColors: ["#009900","#00CC00","#CC0000","#990000"],
                pointSize: 2,
                hideHover: 'always',
                resize: true,
                behaveLikeLine: true,
                smooth: false,
                preUnits: '$'
            });

            /*Power Usage*/
            var powerUsageInterval= (((Number(calculationsData.existingYearByYearPowerCost[0])/12).toFixed(2)/Number($scope.activeProject.power_cost_per_kWh))/5).toFixed(0);
            var existingPowerUsageChart = AmCharts.makeChart("existingPowerUsage", {
                "type": "gauge",
                "axes": [{
                    "axisThickness":1,
                    "axisAlpha":1,
                    "tickAlpha":0,
                    "valueInterval":powerUsageInterval,
                    "bands": [{  "color": "#84b761",  "endValue": powerUsageInterval*2, "innerRadius": "93%", "startValue": 0 },
                        { "color": "#fdd400", "endValue": powerUsageInterval*4, "innerRadius": "92%", "startValue": powerUsageInterval*2 },
                        { "color": "#cc4748", "endValue": powerUsageInterval*6, "innerRadius": "90%", "startValue": powerUsageInterval*4 }
                    ],
                    "bottomText": $scope.activeProject.calculationsData.existingMonthlyPowerUsage,
                    "bottomTextYOffset": -5,
                    "endValue": powerUsageInterval*6
                }],
                "fontSize": 4,
                "arrows": [{"nailRadius": 6 }]
            });
            existingPowerUsageChart.arrows[0].setValue(powerUsageInterval*5);

            var proposedPowerUsageChart = AmCharts.makeChart("proposedPowerUsage", {
                "type": "gauge",
                "axes": [{
                    "axisThickness":1,
                    "axisAlpha":1,
                    "tickAlpha":0,
                    "valueInterval":powerUsageInterval,
                    "bands": [{  "color": "#84b761",  "endValue": powerUsageInterval*2, "innerRadius": "93%", "startValue": 0 },
                        { "color": "#fdd400", "endValue": powerUsageInterval*4, "innerRadius": "92%", "startValue": powerUsageInterval*2 },
                        { "color": "#cc4748", "endValue": powerUsageInterval*6, "innerRadius": "90%", "startValue": powerUsageInterval*4 }
                    ],
                    "bottomText": $scope.activeProject.calculationsData.proposedMonthlyPowerUsage,
                    "bottomTextYOffset": -5,
                    "endValue": powerUsageInterval*6
                }],
                "fontSize": 4,
                "arrows": [{"nailRadius": 6 }]
            });
            proposedPowerUsageChart.arrows[0].setValue(
                (Number(calculationsData.proposedYearByYearPowerCost[0])/12)/(Number($scope.activeProject.power_cost_per_kWh))
            );

            /*Power Cost*/
            var powerCostInterval = ((Number(calculationsData.existingYearByYearPowerCost[0])/12)/5).toFixed(0);
            var existingPowerCostChart = AmCharts.makeChart("existingPowerCost", {
                "type": "gauge",
                "axes": [{
                    "axisThickness":1,
                    "axisAlpha":1,
                    "tickAlpha":0,
                    "valueInterval":powerCostInterval,
                    "bands": [{  "color": "#84b761",  "endValue": powerCostInterval*2, "innerRadius": "93%", "startValue": 0 },
                        { "color": "#fdd400", "endValue": powerCostInterval*4, "innerRadius": "92%", "startValue": powerCostInterval*2 },
                        { "color": "#cc4748", "endValue": powerCostInterval*6, "innerRadius": "90%", "startValue": powerCostInterval*4 }
                    ],
                    "bottomText": $scope.activeProject.calculationsData.existingMonthlyPowerCost,
                    "bottomTextYOffset": -5,
                    "endValue": powerCostInterval*6
                }],
                "fontSize": 4,
                "arrows": [{"nailRadius": 6 }]
            });
            existingPowerCostChart.arrows[0].setValue(powerCostInterval*5);

            var proposedPowerCostChart = AmCharts.makeChart("proposedPowerCost", {
                "type": "gauge",
                "axes": [{
                    "axisThickness":1,
                    "axisAlpha":1,
                    "tickAlpha":0,
                    "valueInterval":powerCostInterval,
                    "bands": [{  "color": "#84b761",  "endValue": powerCostInterval*2, "innerRadius": "93%", "startValue": 0 },
                        { "color": "#fdd400", "endValue": powerCostInterval*4, "innerRadius": "92%", "startValue": powerCostInterval*2 },
                        { "color": "#cc4748", "endValue": powerCostInterval*6, "innerRadius": "90%", "startValue": powerCostInterval*4 }
                    ],
                    "bottomText": $scope.activeProject.calculationsData.proposedMonthlyPowerCost,
                    "bottomTextYOffset": -5,
                    "endValue": powerCostInterval*6
                }],
                "fontSize": 4,
                "arrows": [{"nailRadius": 6 }]
            });
            proposedPowerCostChart.arrows[0].setValue(
                Number(calculationsData.proposedYearByYearPowerCost[0])/12
            );

            /*Maintenance Cost*/
            var maintenanceCostInterval = (Number(calculationsData.existingYearlyMaintenanceCost)/12) > Number(calculationsData.monthlyLeasePaymentStandard) ?
                ((Number(calculationsData.existingYearlyMaintenanceCost)/12)/5).toFixed(0): (calculationsData.monthlyLeasePaymentStandard/5).toFixed(0);
            var existingMaintenanceCostChart = AmCharts.makeChart("existingMaintenanceCost", {
                "type": "gauge",
                "axes": [{
                    "axisThickness":1,
                    "axisAlpha":1,
                    "tickAlpha":0,
                    "valueInterval":maintenanceCostInterval,
                    "bands": [{  "color": "#84b761",  "endValue": maintenanceCostInterval*2, "innerRadius": "93%", "startValue": 0 },
                        { "color": "#fdd400", "endValue":  maintenanceCostInterval*4, "innerRadius": "92%", "startValue":  maintenanceCostInterval*2 },
                        { "color": "#cc4748", "endValue":  maintenanceCostInterval*6, "innerRadius": "90%", "startValue":  maintenanceCostInterval*4 }
                    ],
                    "bottomText": $scope.activeProject.calculationsData.existingMonthlyMaintenanceCost,
                    "bottomTextYOffset": -5,
                    "endValue": maintenanceCostInterval*6
                }],
                "fontSize": 4,
                "arrows": [{"nailRadius": 6 }]
            });
            existingMaintenanceCostChart.arrows[0].setValue(Number(calculationsData.existingYearlyMaintenanceCost)/12);

            var proposedMaintenanceCostChart = AmCharts.makeChart("proposedMaintenanceCost", {
                "type": "gauge",
                "axes": [{
                    "axisThickness":1,
                    "axisAlpha":1,
                    "tickAlpha":0,
                    "valueInterval":maintenanceCostInterval,
                    "bands": [{  "color": "#84b761",  "endValue": maintenanceCostInterval*2, "innerRadius": "93%", "startValue": 0 },
                        { "color": "#fdd400", "endValue":  maintenanceCostInterval*4, "innerRadius": "92%", "startValue":  maintenanceCostInterval*2 },
                        { "color": "#cc4748", "endValue":  maintenanceCostInterval*6, "innerRadius": "90%", "startValue":  maintenanceCostInterval*4 }
                    ],
                    "bottomText": 0.00,
                    "bottomTextYOffset": -5,
                    "endValue": maintenanceCostInterval*6
                }],
                "fontSize": 4,
                "arrows": [{"nailRadius": 6 }]
            });
            proposedMaintenanceCostChart.arrows[0].setValue(0);

            /*Operational Cost*/
            var operationalCostInterval 	  = (((Number(calculationsData.existingYearByYearPowerCost[0])/12) + (Number(calculationsData.existingYearlyMaintenanceCost)/12))/5).toFixed(0);
            $scope.existingMonthlyOperationalCost = commonFactory.toFormattedNumber((Number(calculationsData.existingYearByYearPowerCost[0])/12) + (Number(calculationsData.existingYearlyMaintenanceCost)/12));
            $scope.proposedMonthlyOperationalCost = commonFactory.toFormattedNumber(Number(calculationsData.proposedYearByYearPowerCost[0])/12 + Number($scope.activeView == 'standardShipping'? calculationsData.monthlyLeasePaymentStandard: calculationsData.monthlyLeasePaymentExpedited));

            var existingOperationalCostChart = AmCharts.makeChart("existingMonthlyOperationalCost", {
                "type": "gauge",
                "axes": [{
                    "axisThickness":1,
                    "axisAlpha":1,
                    "tickAlpha":0,
                    "valueInterval":operationalCostInterval,
                    "bands": [{  "color": "#84b761",  "endValue": operationalCostInterval*2, "innerRadius": "93%", "startValue": 0 },
                        { "color": "#fdd400", "endValue":  operationalCostInterval*4, "innerRadius": "92%", "startValue":  operationalCostInterval*2 },
                        { "color": "#cc4748", "endValue":  operationalCostInterval*6, "innerRadius": "90%", "startValue":  operationalCostInterval*4 }
                    ],
                    "bottomText": $scope.existingMonthlyOperationalCost,
                    "bottomTextYOffset": -5,
                    "endValue": operationalCostInterval*6
                }],
                "fontSize": 4,
                "arrows": [{"nailRadius": 6 }]
            });
            existingOperationalCostChart.arrows[0].setValue((Number(calculationsData.existingYearByYearPowerCost[0])/12) + (Number(calculationsData.existingYearlyMaintenanceCost)/12));

            // Assign this to scope because data changes dinamically
            $scope.proposedOperationalCostChart = AmCharts.makeChart("proposedMonthlyOperationalCost", {
                "type": "gauge",
                "axes": [{
                    "axisThickness":1,
                    "axisAlpha":1,
                    "tickAlpha":0,
                    "valueInterval":operationalCostInterval,
                    "bands": [{  "color": "#84b761",  "endValue": operationalCostInterval*2, "innerRadius": "93%", "startValue": 0 },
                        { "color": "#fdd400", "endValue":  operationalCostInterval*4, "innerRadius": "92%", "startValue":  operationalCostInterval*2 },
                        { "color": "#cc4748", "endValue":  operationalCostInterval*6, "innerRadius": "90%", "startValue":  operationalCostInterval*4 }
                    ],
                    "bottomText": $scope.proposedMonthlyOperationalCost,
                    "bottomTextYOffset": -5,
                    "endValue": operationalCostInterval*6
                }],
                "fontSize": 4,
                "arrows": [{"nailRadius": 6 }]
            });
            $scope.proposedOperationalCostChart.arrows[0].setValue(Number(calculationsData.proposedYearByYearPowerCost[0])/12 + Number($scope.activeView == 'standardShipping'? calculationsData.monthlyLeasePaymentStandard: calculationsData.monthlyLeasePaymentExpedited));

            $("#proposedPowerUsage a").remove();
            $("#existingPowerUsage a").remove();
            $("#proposedMaintenanceCost a").remove();
            $("#existingMaintenanceCost a").remove();
            $("#proposedPowerCost a").remove();
            $("#existingPowerCost a").remove();
            $("#existingMonthlyOperationalCost a").remove();
            $("#proposedMonthlyOperationalCost a").remove();

            // Print when page is finished loading
            setTimeout(function(){
                window.print();
            },500);
        });
    };

    // Update map with new pins
    function getLightFixturesPoles() {
        var data = $scope.activeProject.poles;
        var totalLightFixtureQuantity = 0;
        var totalLightFixtureUnitCost = 0.0;
        var totalLightFixtureQuantityExisting = 0;

        // Prepare data for Project Overview  Existing Light fixtures table
        var existingGroupedPoles = new Array();

        // Prepare data for Project Overview Light fixtures table
        var groupedPoles = new Array();

        for (i = 0; i < data.length; i++) {
            /********* Proposed Stats ************/
            totalLightFixtureQuantity += Number(data[i].numOfHeadsProposed);
            totalLightFixtureUnitCost += Number(data[i].LEDunitCost);

            // Extract elements with the same LEDpartNumber
            var group = _.where(data, {LEDpartNumber: data[i].LEDpartNumber});
            // Check if group was not added already to groupedPoles list
            var previouslyAddedPole  = _.where(groupedPoles, {LEDpartNumber: data[i].LEDpartNumber});
            if(previouslyAddedPole.length == 0){
                // If item not repeated, add to list
                if (group.length == 1) {
                    groupedPoles.push(
                        _.pick(group[0], 'LEDpartNumber', 'LEDdesc', 'LEDunitCost', 'numOfHeadsProposed')
                    );
                }
                // if item repeated, calculate the total quantity and unit costs and save it
                else if (group.length > 1) {
                    var totalQuantity = 0;
                    var unitCost = 0;
                    for (var j = 0; j < group.length; j++) {
                        totalQuantity += Number(group[j].numOfHeadsProposed);
                        unitCost += Number(group[j].LEDunitCost);
                    };
                    var auxPole = _.pick(group[0], 'LEDpartNumber', 'LEDdesc', 'LEDunitCost', 'numOfHeadsProposed');
                    auxPole['numOfHeadsProposed'] = totalQuantity;
                    auxPole['LEDunitCost'] = unitCost.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    groupedPoles.push(auxPole);
                }
            }

            /*************** Existing Stats ***********************/
                // Extract existing poles with same bulbID
            totalLightFixtureQuantityExisting += Number(data[i].numOfHeads);

            var existingGroup = _.where(data, {bulbID: data[i].bulbID});

            // Check if existingGroup was not added already to groupedPoles list
            var previouslyAddedExistingPole  = _.where(existingGroupedPoles, {bulbID: data[i].bulbID});
            if(previouslyAddedExistingPole.length == 0){// If item not repeated, add to existing list
                if (existingGroup.length == 1) {
                    existingGroupedPoles.push(
                        _.pick(existingGroup[0], 'numOfHeads', 'bulbDesc', 'bulbID','poleExist')
                    );
                }
                // if item repeated, calculate the total numOfHeads save it
                else if (existingGroup.length > 1) {
                    var totalNumOfHeads = 0;
                    for (var j = 0; j < existingGroup.length; j++) {
                        totalNumOfHeads += Number(existingGroup[j].numOfHeads);
                    };
                    var aux = _.pick(existingGroup[0], 'numOfHeads', 'bulbDesc', 'bulbID','poleExist');
                    aux['numOfHeads'] = totalNumOfHeads;
                    existingGroupedPoles.push(aux);
                }
            }
        };


        $scope.activeProject.existingLightFixtureTablePoles = existingGroupedPoles;
        $scope.activeProject.lightFixtureTotalUnitCost = totalLightFixtureUnitCost.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        $scope.activeProject.lightFixtureTotalQuantity = totalLightFixtureQuantity;

        $scope.activeProject.lightFixtureTotalQuantityExisting = totalLightFixtureQuantityExisting;


        // TODO Calculate existing fixtures fields and add them to scope
        // <td>{{ pole.numExistingFeature }}</td>
        // <td>{{ pole.existingFeatureDesc }}</td>

        return groupedPoles;
    };

    function calculateTotalSavings(data){
        var total = 0;
        for (var i = 0; i < data.yearByYearSavings.length; i++) {
            total += Number(data.yearByYearSavings[1]);
        };
        return total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    projectsFactory.getProjects(function(data){
        $scope.projects = data;
        var group = _.where(data, {project_ID: Number($scope.params.projectId)});
        if(group.length == 1){
            $scope.loadDetails(group[0]);
        }
        else {
            $("#main").html('<div class="alert alert-danger"><b> Project ' + $scope.params.projectId + ' does not exist in our records!</b></div>');
        }
    });
});