//
//  Map.h
//  ArkonLED
//
//  Created by Michael Nation on 9/8/14.
//  Copyright (c) 2014 ArkonLED. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <GoogleMaps/GoogleMaps.h>
#import <CoreLocation/CoreLocation.h>
#import "MarkerInfoDelegate.h"
#import "CalculateArea.h"
#import "MapDetailsDelegate.h"
#import "UserInfoGlobal.h"
#import <QuartzCore/QuartzCore.h>

@interface Map : UIViewController <CLLocationManagerDelegate, GMSMapViewDelegate, MarkerInfoDelegate, UIAlertViewDelegate, MapDetailsDelegate>
{
    int markerCount;
    bool markerWindowIsOpen;
}

@property (strong, nonatomic) GMSMapView *mapView_;
@property (strong, nonatomic) GMSMarker *markerGlobalVar;
@property (strong, nonatomic) GMSMutablePath *path;
@property(nonatomic,retain) CLLocationManager *locationManager;
@property(nonatomic) CLLocationDegrees latitudeCurrentLocation;
@property(nonatomic) CLLocationDegrees longitudeCurrentLocation;

@property (strong, nonatomic) NSMutableArray *arrayMarkers;
@property (strong, nonatomic) NSMutableArray *arrayResponse;


@property (strong, nonatomic) NSString *strNavBarTitle;
@property (strong, nonatomic) NSString *strCostPerKWH;
@property (strong, nonatomic) NSString *strDateOfService;
@property (strong, nonatomic) NSString *strNameOfRep;
@property (strong, nonatomic) NSString *strPhone;
@property (strong, nonatomic) NSString *strEmail;
@property (strong, nonatomic) NSString *strComments;
@property (strong, nonatomic) NSString *strProjectID;
@property (nonatomic) BOOL isNewProject;

@property (strong, nonatomic) IBOutlet UIView *viewEditPrjDetails;

@property (strong, nonatomic) IBOutlet UIActivityIndicatorView *activityIndicator;


@end
