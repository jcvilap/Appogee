arkonLEDApp.controller('MainController',function ($scope, $http, projectsFactory){
	$scope.activeView = 'expeditedShipping';
	$scope.paymentType = 'leaseToOwn';
	var calculationsData = null;
	$scope.projects = [];
	$scope.activeProject = null;
	projectsFactory.getProjects(function(data){
		$scope.projects = data;
	});

	// Infowindox on click -- Load project
    $(document).on('click', '.infoW', function(e){
        var str = $(this).attr("id");
        var id = str.substring(10, str.length);
        $("#" + id).trigger('click');
    });

    $(".action-icon").click(function(){
    	// Edit
    	if($(this).hasClass("fa-pencil-square-o")){
    		$(this).hide();
    		$(".fa-floppy-o").show();
			$("#commentsArea").prop('disabled', false).focus();

    	}

    	else{
    		var value = $("#commentsArea").val();
    		// TODO Test
    		$http.post('iOS/Projects/updateProject.php', {
    			comments: value
    		});
    		$(this).hide();
    		$(".fa-pencil-square-o").show();
    		$("#commentsArea").prop('disabled', true);
    	}
    });

    // Toggle Detail Panels
    $('#poBnt').click(function(){ 
    	$('#ldPanel').hide(); 
    	$('#spPanel').hide(); 
    	$('#detailsContainer').switchClass( "col-sm-12", "col-sm-8", 1000, "easeInOutQuad", function(){
    		$('#tableContainer').show();
    		$('#map-canvas').show();
    	});
    	$('#poPanel').slideDown("slow"); 

    });
    $('#ldBnt').click(function(){ 
    	$('#poPanel').hide(); 
    	$('#spPanel').hide(); 
    	$('#detailsContainer').switchClass( "col-sm-12", "col-sm-8", 1000, "easeInOutQuad", function(){
    		$('#tableContainer').show();
    		$('#map-canvas').show();
    	});
    	$('#ldPanel').slideDown("slow"); 
    });
    $('#spBnt').click(function(){  
    	$('#poPanel').hide(); 
    	$('#ldPanel').hide(); 

    	// Extra DOM manipulation when going to the sale s presentation. Revert back when clicking out this button
    	$('#tableContainer').hide();
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
					labels: ['Maintenance Costs', 'Existing Power Consuption', 'LED Lease Costs', 'LED Power Consuption'],
					lineColors: ['DarkSalmon ', 'Brown', 'DarkSeaGreen', 'LimeGreen'],
					pointSize: 2,
					hideHover: 'auto',
					resize: true,
					behaveLikeLine: true
				});

	    		var existingPowerUsageChart = AmCharts.makeChart("existingPowerUsage", {
				    "type": "gauge",  
				    "axes": [{
				        "axisThickness":1,
				        "axisAlpha":1,
				        "tickAlpha":0,
				        "valueInterval":40,
				        "bands": [{  "color": "#84b761",  "endValue": 90, "innerRadius": "93%", "startValue": 0 },
				         		  { "color": "#fdd400", "endValue": 130, "innerRadius": "92%", "startValue": 90 },
				         		  { "color": "#cc4748", "endValue": 220, "innerRadius": "90%", "startValue": 130 }
				         ],
				        "bottomText": $scope.activeProject.calculationsData.existingMonthlyPowerUsage + "W",
				        "bottomTextYOffset": 8,
				        "endValue": 220
				    }],
    				"fontSize": 8,
				    "arrows": [{}]
				});

				var proposedPowerUsageChart = AmCharts.makeChart("proposedPowerUsage", {
				    "type": "gauge",   
				    "axes": [{
				        "axisThickness":1,
				        "axisAlpha":1,
				        "tickAlpha":0,
				        "valueInterval":40,
				        "bands": [{  "color": "#84b761",  "endValue": 90, "innerRadius": "93%", "startValue": 0 },
				         		  { "color": "#fdd400", "endValue": 130, "innerRadius": "92%", "startValue": 90 },
				         		  { "color": "#cc4748", "endValue": 220, "innerRadius": "90%", "startValue": 130 }
				         ],
				        "bottomText": $scope.activeProject.calculationsData.proposedMonthlyPowerUsage + "W",
				        "bottomTextYOffset": 8,
				        "endValue": 220
				    }],
    				"fontSize": 8,
				    "arrows": [{}]
				});

				var existingPowerCostChart = AmCharts.makeChart("existingPowerCost", {
				    "type": "gauge",   
				    "axes": [{
				        "axisThickness":1,
				        "axisAlpha":1,
				        "tickAlpha":0,
				        "valueInterval":40,
				        "bands": [{  "color": "#84b761",  "endValue": 90, "innerRadius": "93%", "startValue": 0 },
				         		  { "color": "#fdd400", "endValue": 130, "innerRadius": "92%", "startValue": 90 },
				         		  { "color": "#cc4748", "endValue": 220, "innerRadius": "90%", "startValue": 130 }
				         ],
				        "bottomText": "$" + $scope.activeProject.calculationsData.existingMonthlyPowerCost,
				        "bottomTextYOffset": 8,
				        "endValue": 220
				    }],
    				"fontSize": 8,
				    "arrows": [{}]
				});

				var proposedPowerCostChart = AmCharts.makeChart("proposedPowerCost", {
				    "type": "gauge",   
				    "axes": [{
				        "axisThickness":1,
				        "axisAlpha":1,
				        "tickAlpha":0,
				        "valueInterval":40,
				        "bands": [{  "color": "#84b761",  "endValue": 90, "innerRadius": "93%", "startValue": 0 },
				         		  { "color": "#fdd400", "endValue": 130, "innerRadius": "92%", "startValue": 90 },
				         		  { "color": "#cc4748", "endValue": 220, "innerRadius": "90%", "startValue": 130 }
				         ],
				        "bottomText": "$" + $scope.activeProject.calculationsData.proposedMonthlyPowerCost,
				        "bottomTextYOffset": 8,
				        "endValue": 220
				    }],
    				"fontSize": 8,
				    "arrows": [{}]
				});

				var existingMaintenanceCostChart = AmCharts.makeChart("existingMaintenanceCost", {
				    "type": "gauge",   
				    "axes": [{
				        "axisThickness":1,
				        "axisAlpha":1,
				        "tickAlpha":0,
				        "valueInterval":40,
				        "bands": [{  "color": "#84b761",  "endValue": 90, "innerRadius": "93%", "startValue": 0 },
				         		  { "color": "#fdd400", "endValue": 130, "innerRadius": "92%", "startValue": 90 },
				         		  { "color": "#cc4748", "endValue": 220, "innerRadius": "90%", "startValue": 130 }
				         ],
				        "bottomText": "$" + $scope.activeProject.calculationsData.existingMonthlyMaintenanceCost,
				        "bottomTextYOffset": 8,
				        "endValue": 220
				    }],
    				"fontSize": 8,
				    "arrows": [{}]
				});

				var proposedMaintenanceCostChart = AmCharts.makeChart("proposedMaintenanceCost", {
				    "type": "gauge",   
				    "axes": [{
				        "axisThickness":1,
				        "axisAlpha":1,
				        "tickAlpha":0,
				        "valueInterval":40,
				        "bands": [{  "color": "#84b761",  "endValue": 90, "innerRadius": "93%", "startValue": 0 },
				         		  { "color": "#fdd400", "endValue": 130, "innerRadius": "92%", "startValue": 90 },
				         		  { "color": "#cc4748", "endValue": 220, "innerRadius": "90%", "startValue": 130 }
				         ],
				        "bottomText":"$" + $scope.activeProject.calculationsData.monthlyLeasePaymentStandard,
				        "bottomTextYOffset": 8,
				        "endValue": 220
				    }],
    				"fontSize": 8,
				    "arrows": [{}]
				});

				// Set arrows for now
				existingPowerUsageChart.arrows[0].setValue(160);
          		proposedPowerUsageChart.arrows[0].setValue(40);
          		existingPowerCostChart.arrows[0].setValue(200);
          		proposedPowerCostChart.arrows[0].setValue(60);
          		existingMaintenanceCostChart.arrows[0].setValue(95);
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
			$scope.activeProject.stats = calculateChartPoints();
			$scope.activeView = 'expeditedShipping';
			$scope.areaChart.setData($scope.activeProject.stats);
		}
	});
	$('#standardShippingBt').click(function(){ 
		// Update data to standard shipping
		if($scope.activeView != 'standardShipping'){
			$scope.activeProject.stats = calculateChartPoints();
			$scope.activeView = 'standardShipping';
			$scope.areaChart.setData($scope.activeProject.stats);
		}
	});
	$('#upfrontPurchaseBt').click(function(){ 
		// Update data to expedited shipping
		if($scope.PaymentType != 'upfrontPurchase'){
			$scope.activeProject.stats = calculateChartPoints();
			$scope.PaymentType = 'upfrontPurchase';
			$scope.areaChart.setData($scope.activeProject.stats);
		}
	});
	$('#leaseToOwnBt').click(function(){ 
		// Update data to standard shipping
		if($scope.activeView != 'leaseToOwn'){
			$scope.activeProject.stats = calculateChartPoints();
			$scope.PaymentType = 'leaseToOwn';
			$scope.areaChart.setData($scope.activeProject.stats);
		}
	});

    // Expand or collapse headers
    $('.page-header').click(function(){
        $(this).next().slideToggle('slow');
        var icon = $(this).find('i');
        $(icon).toggleClass("fa-minus-square-o");
        $(icon).toggleClass("fa-plus-square-o");
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
			calculationsData = data;
			$scope.activeProject.calculationsData = data;
			$scope.activeProject.stats = calculateChartPoints();
			// TODO:Check with Arkon this field
			$scope.activeProject.totalSavings = calculateTotalSavings(data);
			// Assign value for UI use
			$scope.activeProject.calculationsData.existingMonthlyMaintenanceCost = (Number(data.existingYearlyMaintenanceCost)/12).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
			$scope.activeProject.calculationsData.existingMonthlyPowerCost = (Number(data.existingYearByYearPowerCost[0])/12).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'); 
			$scope.activeProject.calculationsData.proposedMonthlyPowerCost = (Number(data.proposedYearByYearPowerCost[0])/12).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'); 
        	$scope.activeProject.calculationsData.existingMonthlyPowerUsage = ($scope.activeProject.calculationsData.existingMonthlyPowerCost/Number($scope.activeProject.power_cost_per_kWh)).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        	$scope.activeProject.calculationsData.proposedMonthlyPowerUsage = ($scope.activeProject.calculationsData.proposedMonthlyPowerCost/Number($scope.activeProject.power_cost_per_kWh)).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        	$scope.activeProject.calculationsData.powerSavings = Number(data.existingYearByYearPowerCost[0]) - Number(data.proposedYearByYearPowerCost[0]);
        
		});

		$('#poBnt').trigger('click');
	};

	calculateChartPoints = function(){
		var stats = new Array();
		if($scope.activeView == 'expeditedShipping' && $scope.paymentType == 'upfrontPurchase'){
			for (var i = 0; i < 10; i++) {
				stats[i] = {
					year: (new Date().getFullYear() + i + 1).toString(),
					existingPowerCost: calculationsData.existingYearByYearPowerCost[i],
					existingMantenanceCost: calculationsData.existingYearByYearPowerCost[i] + (calculationsData.existingYearlyMaintenanceCost/100),
					proposedPowerCost: calculationsData.proposedYearByYearPowerCost[i],
					LEDLeasePayment: calculationsData.proposedYearByYearPowerCost[i],
					savings: calculationsData.yearByYearSavings[i],
				};
			};
		}
		else if($scope.activeView == 'standardShipping' && $scope.paymentType == 'upfrontPurchase'){
			for (var i = 0; i < 10; i++) {
				stats[i] = {
					year: (new Date().getFullYear() + i + 1).toString(),
					existingPowerCost: calculationsData.existingYearByYearPowerCost[i] ,
					existingMantenanceCost: calculationsData.existingYearByYearPowerCost[i] + (calculationsData.existingYearlyMaintenanceCost/100),
					proposedPowerCost: calculationsData.proposedYearByYearPowerCost[i],
					LEDLeasePayment: calculationsData.proposedYearByYearPowerCost[i],
					savings: calculationsData.yearByYearSavings[i],	
				};
			};
		}
		else if($scope.activeView == 'expeditedShipping' && $scope.paymentType == 'leaseToOwn'){
			for (var i = 0; i < 10; i++) {
				stats[i] = {
					year: (new Date().getFullYear() + i + 1).toString(),
					existingPowerCost: calculationsData.existingYearByYearPowerCost[i],
					existingMantenanceCost: calculationsData.existingYearByYearPowerCost[i] + (calculationsData.existingYearlyMaintenanceCost/100),
					proposedPowerCost: calculationsData.proposedYearByYearPowerCost[i],
					LEDLeasePayment: i < 6 ? calculationsData.proposedYearByYearPowerCost[i] + (calculationsData.yearlyLeasePaymentExpedited/100): calculationsData.proposedYearByYearPowerCost[i],
					savings: calculationsData.yearByYearSavings[i],
				};
			};
		}
		else if($scope.activeView == 'standardShipping' && $scope.paymentType == 'leaseToOwn'){
			for (var i = 0; i < 10; i++) {
				stats[i] = {
					year: (new Date().getFullYear() + i + 1).toString(),
					existingPowerCost: calculationsData.existingYearByYearPowerCost[i] ,
					existingMantenanceCost: calculationsData.existingYearByYearPowerCost[i] + (calculationsData.existingYearlyMaintenanceCost/100),
					proposedPowerCost: calculationsData.proposedYearByYearPowerCost[i],
					LEDLeasePayment: i < 6 ?calculationsData.proposedYearByYearPowerCost[i] + (calculationsData.yearlyLeasePaymentStandard/100): calculationsData.proposedYearByYearPowerCost[i],
					savings: calculationsData.yearByYearSavings[i],	
				};
			};
		}
		

		return stats;
	};

	$scope.activateMarker = function (poleID){
		for (var i = 0; i < markers.length; i++) {
			if(markers[i].key == poleID){
				google.maps.event.trigger(markers[i].marker, 'click');
				break;
			}
		};
	}

	$scope.initTable = function(){
		$('#projectsTable').dataTable({
			"drawCallback": function( settings ) {
			    var api = this.api();
			    var projectIds = new Array();
			    // Rows on the screen 
			    var tableData = api.rows( {page:'current'} ).data();
			    // Get Project Ids
			    for (var i = 0; i < tableData.length; i++) {
			        projectIds[i] = tableData[i][0];
			    };
			    // Draw filtered projects in the map
			    $scope.drawMap(projectIds, 'projects');
			}
		});
	};

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
		if (type == 'projects' ){
			activeIds = new Array();
			for (var i = 0; i < list.length; i++) {
				activeIds[i] = Number(list[i].substring(list[i].indexOf(">") + 1,list[i].lastIndexOf("<")));
			};
			var projects = $scope.projects;
			LatLngList = new Array();
			indexes = new Array();

			for (var j = 0; j < activeIds.length; j++) {
				for (var i = 0; i < projects.length; i++) {
					if(projects[i].project_ID == activeIds[j]) {
						indexes[j] = i;
						LatLngList.push(new google.maps.LatLng(projects[i].project_latitude, projects[i].project_longitude));
					}
				}
			}
			iterator = 0;
			//  Create a new viewpoint bound
			var bounds = new  google.maps.LatLngBounds();
			// Increase the bounds to take every point 
			for (var i = 0; i < LatLngList.length; i++) {
			  	setTimeout(function() {
			      	addMarker(projects, "projects");
			    }, i * 150);
			  	bounds.extend (LatLngList[i]);
			}
			//  Fit bounds into the map
			$scope.map.fitBounds (bounds);
			$("#map-canvas").css("position","fixed");
		}
		else if (type == 'poles'){
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
		// Prepare data for Project Overview Light fixtures table
		var groupedPoles = new Array(); 
        for (i = 0; i < data.length; i++) { 
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
        };
        $scope.activeProject.lightFixtureTotalUnitCost = totalLightFixtureUnitCost.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        $scope.activeProject.lightFixtureTotalQuantity = totalLightFixtureQuantity;
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