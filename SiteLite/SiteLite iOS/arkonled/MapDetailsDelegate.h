//
//  MapDetailsDelegate.h
//  ArkonLED
//
//  Created by Michael Nation on 9/21/14.
//  Copyright (c) 2014 ArkonLED. All rights reserved.
//

#import <UIKit/UIKit.h>

@protocol MapDetailsDelegate <NSObject>

-(void)doneMapDetails:(NSMutableDictionary *)dicProjectDetails andStatus:(BOOL)isNewProject;

@end

@interface MapDetailsDelegate : UITableViewController <UITextFieldDelegate>

@property(strong, nonatomic) id <MapDetailsDelegate> delegate;

@property (strong, nonatomic) IBOutlet UITextField *txtProjectName;
@property (strong, nonatomic) NSString *strProjectName;
@property (strong, nonatomic) IBOutlet UITextField *txtCost;
@property (strong, nonatomic) NSString *strCost;
@property (strong, nonatomic) IBOutlet UITextField *txtDateOfService;
@property (strong, nonatomic) NSString *strDateOfService;

@property(nonatomic) BOOL isNewProject;

//Contact Information
@property (strong, nonatomic) IBOutlet UITextField *txtNameOfRep;
@property (strong, nonatomic) NSString *strNameOfRep;
@property (strong, nonatomic) IBOutlet UITextField *txtPhone;
@property (strong, nonatomic) NSString *strPhone;
@property (strong, nonatomic) IBOutlet UITextField *txtEmail;
@property (strong, nonatomic) NSString *strEmail;

//Comments
@property (strong, nonatomic) IBOutlet UITextView *txtComments;
@property (strong, nonatomic) NSString *strComments;

@end
