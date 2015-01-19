//
//  MarkerInfoDelegate.h
//  ArkonLED
//
//  Created by Michael Nation on 9/9/14.
//  Copyright (c) 2014 ArkonLED. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <GoogleMaps/GoogleMaps.h>
#import "StaticDataTableViewController.h"
#import "UserInfoGlobal.h"
#import "NSData+Base64.h"
#import "LightPoleLargeImage.h"

@protocol MarkerInfoDelegate <NSObject>

-(void)doneMarkerInfo:(GMSMarker *)marker isNewMarker:(BOOL)isNewMarker;

@end

@interface MarkerInfoDelegate : StaticDataTableViewController /*UITableViewController*/ <UIPickerViewDelegate, UIPickerViewDataSource, UIImagePickerControllerDelegate, UINavigationControllerDelegate>

@property(strong, nonatomic) id <MarkerInfoDelegate> delegate;

@property(strong, nonatomic) GMSMarker *marker;
@property(strong, nonatomic) GMSMarker *markerPrevious;
@property(strong, nonatomic) NSNumber *markerCount;
@property(strong, nonatomic) NSDictionary *dicMarkerGlobal;

@property (strong, nonatomic) IBOutlet UIBarButtonItem *btnCancel;

@property (strong, nonatomic) IBOutlet UISwitch *switchPrepopulate;

//Current
@property (strong, nonatomic) IBOutlet UISwitch *switchPoleExist;
@property (strong, nonatomic) IBOutlet UITextField *txtNumOfHeads;
@property (strong, nonatomic) IBOutlet UITableViewCell *cellNumOfHeads;
@property (strong, nonatomic) IBOutlet UITextField *txtWattage;
@property (strong, nonatomic) IBOutlet UITableViewCell *cellWattage;
@property (strong, nonatomic) IBOutlet UIPickerView *pickerBulbType;
@property (strong, nonatomic) NSArray *arrayBulbTypes;
@property (strong, nonatomic) NSString *strSelectedBulbType;
@property (strong, nonatomic) NSString *strSelectedBulbID;
@property (strong, nonatomic) IBOutlet UITableViewCell *cellBulbTypes;
@property (strong, nonatomic) IBOutlet UISegmentedControl *segContAssemblyType;


//Proposed
@property (strong, nonatomic) IBOutlet UISwitch *switchOneToOne;
@property (strong, nonatomic) IBOutlet UITextField *txtNumOfHeadsProposed;
@property (strong, nonatomic) IBOutlet UITableViewCell *cellNumOfHeadsProposed;
@property (strong, nonatomic) IBOutlet UIPickerView *pickerLEDFixtures;
@property (strong, nonatomic) NSMutableArray *arrayLEDFixtures;
@property (strong, nonatomic) NSMutableArray *arrayShoebox;
@property (strong, nonatomic) NSMutableArray *arrayWallpack;
@property (strong, nonatomic) NSString *strSelectedLEDFixtureID;
@property (strong, nonatomic) IBOutlet UITableViewCell *cellModalTypes;
@property (strong, nonatomic) IBOutlet UISwitch *switchBracket;
@property (strong, nonatomic) IBOutlet UITableViewCell *cellBracket;
@property (strong, nonatomic) IBOutlet UITextField *txtPoleHeight;
@property (strong, nonatomic) IBOutlet UITableViewCell *cellPoleHeight;
@property (strong, nonatomic) IBOutlet UIButton *btnRemoveImage;
@property (strong, nonatomic) IBOutlet UIButton *btnImage;
@property (strong, nonatomic) IBOutlet UITableViewCell *cellImage;

//Cells
@property (strong, nonatomic) IBOutlet UITableViewCell *cellPrepopulate;
@property (strong, nonatomic) IBOutlet UITableViewCell *cellDeleteMarker;


@end
