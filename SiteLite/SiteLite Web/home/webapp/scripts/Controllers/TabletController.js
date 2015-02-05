arkonLEDApp.controller('TabletController',function ($scope, $http, $routeParams, projectsFactory, commonFactory, loginFactory){
	$scope.params = $routeParams;
	$scope.paymentType = 'leaseToOwn';
	$scope.activeView = 'standardShipping';
	var calculationsData = null;
	$scope.projects = [];
	$scope.activeProject = null;
	$scope.baseUrl = commonFactory.baseUrl;
	$scope.toFormattedNumber = commonFactory.toFormattedNumber;

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

	$("body").css('padding-top', '0');

    // Toggle Detail Panels
    $('#poBnt').click(function(){ 
    	$('#spPanel').hide(); 
    	$('#detailsContainer').switchClass( "col-sm-12", "col-sm-8", 1000, "easeInOutQuad", function(){
    		$('#map-canvas').show();
    	});
    	$('#poPanel').slideDown("slow"); 
    });
    $('#spBnt').click(function(){  
    	$('#poPanel').hide(); 

    	// Extra DOM manipulation when going to the sales presentation. Revert back when clicking out this button
    	$('#map-canvas').hide();
    	$('#spPanel').slideDown("slow"); 

		$('#detailsContainer').switchClass( "col-sm-8", "col-sm-12", 1000, "easeInOutQuad", function(){
    		if(!$scope.areaChartCreated){
    			// Init chart on first click
	    		$scope.areaChartCreated = true;
	    		$scope.areaChart = Morris.Area({
					element: 'morris-area-chart',
					data: $scope.activeProject.stats ,
					xkey: 'year',
					ykeys: ['existingMantenanceCost', 'existingPowerCost', 'LEDLeasePayment', 'proposedPowerCost'],
					labels: ['Maintenance Savings', 'Power Savings', 'LED Finance Payments', 'LED Power Costs'],
					lineColors: ["#009900","#00CC00","#CC0000","#990000"],
					pointSize: 2,
					hideHover: 'auto',
					resize: true,
					behaveLikeLine: true
				});

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
				        "bottomTextYOffset": 8,
				        "endValue": powerUsageInterval*6
				    }],
    				"fontSize": 8,
				    "arrows": [{}]
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
				        "bottomTextYOffset": 8,
				        "endValue": powerUsageInterval*6
				    }],
    				"fontSize": 8,
				    "arrows": [{}]
				});
				proposedPowerUsageChart.arrows[0].setValue(
					(Number(calculationsData.proposedYearByYearPowerCost[0])/12)/(Number($scope.activeProject.power_cost_per_kWh))
				);

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
				        "bottomTextYOffset": 8,
				        "endValue": powerCostInterval*6
				    }],
    				"fontSize": 8,
				    "arrows": [{}]
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
				        "bottomTextYOffset": 8,
				        "endValue": powerCostInterval*6
				    }],
    				"fontSize": 8,
				    "arrows": [{}]
				});
				proposedPowerCostChart.arrows[0].setValue(
					Number(calculationsData.proposedYearByYearPowerCost[0])/12
				);

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
				        "bottomTextYOffset": 8,
				        "endValue": maintenanceCostInterval*6
				    }],
    				"fontSize": 8,
				    "arrows": [{}]
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
				        "bottomText":0.00,
				        "bottomTextYOffset": 8,
				        "endValue": maintenanceCostInterval*6
				    }],
    				"fontSize": 8,
				    "arrows": [{}]
				});
          		proposedMaintenanceCostChart.arrows[0].setValue(0);


				$("#proposedPowerUsage a").remove();
				$("#existingPowerUsage a").remove();
				$("#proposedMaintenanceUsage a").remove();
				$("#existingMaintenanceUsage a").remove();
				$("#proposedPowerCost a").remove();
				$("#existingPowerCost a").remove();
			}
			else {
	    		$scope.areaChart.setData($scope.activeProject.stats);
	    	}
		});
	});

	$('#expeditedShippingBt').click(function(){ 
		// Update data to expedited shipping
		if($scope.activeView != 'expeditedShipping'){
			$scope.activeView = 'expeditedShipping';
			$('#expeditedShippingBt').toggleClass('radio-btn-selected');
			$('#standardShippingBt').toggleClass('radio-btn-selected');
			commonFactory.updateScope($scope);
			$scope.activeProject.stats = commonFactory.calculateChartPoints(calculationsData);
			$scope.areaChart.setData($scope.activeProject.stats);
		}
	});
	$('#standardShippingBt').click(function(){ 
		// Update data to standard shipping
		if($scope.activeView != 'standardShipping'){
			$scope.activeView = 'standardShipping';
			$('#expeditedShippingBt').toggleClass('radio-btn-selected');
			$('#standardShippingBt').toggleClass('radio-btn-selected');
			commonFactory.updateScope($scope);
			$scope.activeProject.stats = commonFactory.calculateChartPoints(calculationsData);
			$scope.areaChart.setData($scope.activeProject.stats);
		}
	});
	$('#upFrontPurchaseBt').click(function(){ 
		// Update data to expedited shipping
		if($scope.paymentType != 'upFrontPurchase'){
			$scope.paymentType = 'upFrontPurchase';
			$('#upFrontPurchaseBt').toggleClass('radio-btn-selected');
			$('#leaseToOwnBt').toggleClass('radio-btn-selected');
			commonFactory.updateScope($scope);
			$scope.activeProject.stats = commonFactory.calculateChartPoints(calculationsData);
			$scope.areaChart.setData($scope.activeProject.stats);
		}
	});
	$('#leaseToOwnBt').click(function(){ 
		// Update data to standard shipping
		if($scope.paymentType != 'leaseToOwn'){
			$scope.paymentType = 'leaseToOwn';
			$('#upFrontPurchaseBt').toggleClass('radio-btn-selected');
			$('#leaseToOwnBt').toggleClass('radio-btn-selected');
			commonFactory.updateScope($scope);
			$scope.activeProject.stats = commonFactory.calculateChartPoints(calculationsData);
			$scope.areaChart.setData($scope.activeProject.stats);
		}
	});

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
			$scope.drawMap(data, 'poles');

			// Collapse Projects panel only if it is not visible
			if($('#projectsPanel').is(":visible")){
				$("#activeProjectHeader").trigger('click');
			}
			// Show details container if it is not visible
			if(! $('#detailsContainer').is(":visible") ){
				$('#detailsContainer').show('slow');
			}
			// Oper details panel if it is not visible
			if(! $('#detailMiddleContainer').is(":visible") ){
				$("#detailsPanelHeader").trigger('click');
			}
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
        	$scope.activeProject.calculationsData.proposedMonthlyPowerUsage = commonFactory.toFormattedNumber(($scope.activeProject.calculationsData.proposedMonthlyPowerCost/Number($scope.activeProject.power_cost_per_kWh)));
        	$scope.activeProject.calculationsData.powerSavings = commonFactory.toFormattedNumber(Number(data.existingYearByYearPowerCost[0]) - Number(data.proposedYearByYearPowerCost[0]));
			
			$scope.activeProject.calculationsData.totalPowerSavings = commonFactory.toFormattedNumber((Number(data.existingYearByYearPowerCost[5]) - Number(data.proposedYearByYearPowerCost[4]) * 10));
			
        	$scope.activeProject.calculationsData.immediateMonthlySavingsExpedited = commonFactory.toFormattedNumber(
        		Number(data.existingYearlyMaintenanceCost)/12 + 
        		Number(data.existingYearByYearPowerCost[0])/12 - 
        		Number(data.monthlyLeasePaymentExpedited)
        	);
        	$scope.activeProject.calculationsData.immediateMonthlySavingsStandard = commonFactory.toFormattedNumber(
        		Number(data.existingYearlyMaintenanceCost)/12 + 
        		Number(data.existingYearByYearPowerCost[0])/12 - 
        		Number(data.monthlyLeasePaymentStandard)
        	);
        	$scope.activeProject.calculationsData.monthlyLeasePaymentStandard = commonFactory.toFormattedNumber(data.monthlyLeasePaymentStandard);
			$scope.activeProject.calculationsData.monthlyLeasePaymentExpedited = commonFactory.toFormattedNumber(data.monthlyLeasePaymentExpedited);
			$scope.activeProject.calculationsData.existingMonthlyMaintenanceCost = commonFactory.toFormattedNumber(Number(data.existingYearlyMaintenanceCost)/12);
			$scope.activeProject.calculationsData.existingYearlyMaintenanceCost = commonFactory.toFormattedNumber(Number(data.existingYearlyMaintenanceCost));
			
			$scope.activeProject.calculationsData.existingTotalMaintenanceCost = commonFactory.toFormattedNumber(Number(data.existingYearlyMaintenanceCost)*10);
		});

		$('#poBnt').trigger('click');
	};

	$scope.evenTableHeights = function(){
		var height2 =  (_.size($scope.activeProject.lightFixtureTablePoles)+1)*37 + 60;
		var height1 =  _.size($scope.activeProject.existingLightFixtureTablePoles)*37 + 60;
		if (height1 > height2) {
			$("#proposedLightFixtureTable").height(height1);
			$("#existingLightFixtureTable").height(height1);
		}else if (height2 > height1) {
			$("#proposedLightFixtureTable").height(height2);
			$("#existingLightFixtureTable").height(height2);
		};
       
        $('#progressModal').modal("hide");  
	}

	$scope.activateMarker = function (poleID){
		for (var i = 0; i < markers.length; i++) {
			if(markers[i].key == poleID){
				google.maps.event.trigger(markers[i].marker, 'click');
				break;
			}
		};
	}

	/******************************************
		Maps config and functions
	*******************************************/
	$scope.map = new google.maps.Map(document.getElementById('map-canvas'), {mapTypeId: google.maps.MapTypeId.HYBRID});

	// Maps global instances
	var LatLngList = {};
	var markers = [];
	var iterator = {};
	var indexes = {};
	var activeIds = {};
	var lastInfoWindow = null;

    // Update map with new pins 
	$scope.drawMap = function( list, type) {
		deleteAllMarkers();
		if (type == 'poles'){
			LatLngList = new Array();

			for (var j = 0; j < list.length; j++) {
				LatLngList.push(new google.maps.LatLng(list[j].poleLat, list[j].poleLong));
			}
			iterator = 0;
			//  Create a new viewpoint bound
			var bounds = new google.maps.LatLngBounds();
			// Increase the bounds to take every point
			for (var i = 0; i < LatLngList.length; i++) {
			  	setTimeout(function() {
			      	addMarker(list, "poles");
			    }, i * 150);
			  	bounds.extend (LatLngList[i]);
			}
			//  Fit bounds into the map
			$scope.map.fitBounds (bounds);
			$("#map-canvas").css("position","fixed");
		}
	};

	function addMarker(list, type){
		var marker = null;
		var infowindow = null;
		marker = new google.maps.Marker({
			position: LatLngList[iterator],
			map: $scope.map,
			draggable: false,
			animation: google.maps.Animation.DROP
		});
		if(type == "projects"){
			var index = indexes[iterator];
			infowindow = new google.maps.InfoWindow({
	        	content: '<div class="scrollFix"><a href="#" class="infoW" id="infoWindow'+ activeIds[iterator] +'">' + activeIds[iterator] + ' </a> <br/>' + list[index].project_name + '</div>'
			});
			markers.push({"key":list[index].project_ID, "marker": marker});
		}
		else if (type == "poles"){
			infowindow = new google.maps.InfoWindow({
	        	content: '<div class="scrollFix"><a href="#" class="infoW" id="infoWindow'+ list[iterator].poleID +'">' + list[iterator].poleID + ' </a> <br/>' + list[iterator].LEDdesc + '</div>'
			});
			markers.push({"key":list[iterator].poleID, "marker": marker});
		}

		google.maps.event.addListener(marker, 'click', function() {
		    if(lastInfoWindow){
		    	lastInfoWindow.close();
		    }
		    lastInfoWindow = infowindow;
		    infowindow.open($scope.map, marker);
		});

	  	iterator++;
	};

	function deleteAllMarkers(){
		for (var i = 0; i < markers.length; i++) {
			markers[i].marker.setMap(null);
		};
		markers = new Array();
	};

	// Update map with new pins 
	function getLightFixturesPoles() {
		var data = $scope.activeProject.poles;
		var totalLightFixtureQuantity = 0;
		var totalLightFixtureUnitCost = 0.0;
		
		var totalLightFixtureQuantityExisting = 0;

		// existing fields
		var numExistingFeature = 0, existingFeatureDesc ='';

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
                    	_.pick(existingGroup[0], 'numOfHeads', 'bulbDesc', 'bulbID')
                    );
                }                
                // if item repeated, calculate the total numOfHeads save it
                else if (existingGroup.length > 1) {
                    var totalNumOfHeads = 0;
                    for (var j = 0; j < existingGroup.length; j++) {
                        totalNumOfHeads += Number(existingGroup[j].numOfHeadsProposed);
                    };
                    var aux = _.pick(existingGroup[0], 'numOfHeads', 'bulbDesc', 'bulbID');
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

});