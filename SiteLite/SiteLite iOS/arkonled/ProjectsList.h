//
//  ProjectsList.h
//  ArkonLED
//
//  Created by Michael Nation on 9/7/14.
//  Copyright (c) 2014 ArkonLED. All rights reserved.
//

#import <UIKit/UIKit.h>
#include "Map.h"
#include "MapDetailsDelegate.h"
#include "UserInfoGlobal.h"
#import "SSKeychain.h"
#import "SSKeychainQuery.h"
#import <MessageUI/MessageUI.h>
#import "Presentation.h"

@interface ProjectsList : UITableViewController <UIAlertViewDelegate, MFMailComposeViewControllerDelegate>

@property (strong, nonatomic) NSString *strAssemblyType;
@property (strong, nonatomic) NSMutableArray *activeProjects;
@property (strong, nonatomic) NSMutableArray *inactiveProjects;
@property (strong, nonatomic) NSMutableArray *closedProjects;
@property (strong, nonatomic) NSMutableDictionary *dicEmailClient;

//Toolbar Assemby Types Buttons
@property (strong, nonatomic) IBOutlet UIBarButtonItem *btnActive;
@property (strong, nonatomic) IBOutlet UIBarButtonItem *btnInactive;
@property (strong, nonatomic) IBOutlet UIBarButtonItem *btnClosed;


//Existing Project
@property (strong, nonatomic) NSString *strProjectID;
@property (strong, nonatomic) NSString *strProjectTitle;
@property (strong, nonatomic) NSString *strCostPerKWH;

@end
