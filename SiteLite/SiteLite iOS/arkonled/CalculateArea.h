//
//  CalculateArea.h
//  ArkonLED
//
//  Created by Michael Nation on 9/13/14.
//  Copyright (c) 2014 ArkonLED. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <GoogleMaps/GoogleMaps.h>
#import <CoreLocation/CoreLocation.h>
#import "UserInfoGlobal.h"

@interface CalculateArea : UIViewController <CLLocationManagerDelegate, GMSMapViewDelegate, UIAlertViewDelegate>

@property (strong, nonatomic) GMSMapView *mapView_;
@property(nonatomic,retain) CLLocationManager *locationManager;
@property(nonatomic) CLLocationDegrees latitudeCurrentLocation;
@property(nonatomic) CLLocationDegrees longitudeCurrentLocation;

@property (strong, nonatomic) NSMutableArray *arrayAreaMarkers;
@property (strong, nonatomic) NSMutableArray *arrayLightPoles;
@property (strong, nonatomic) NSString *strProjectID;

//Drawing Polygon Area
@property (strong, nonatomic) GMSMutablePath *rect;
@property (strong, nonatomic) GMSPolygon *polygon;
@property (strong, nonatomic) GMSPolyline *polyline;
@property (strong, nonatomic) IBOutlet UIBarButtonItem *lblArea;

@property (strong, nonatomic) IBOutlet UIActivityIndicatorView *activityIndicator;

@end
