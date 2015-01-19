//
//  Login.h
//  ArkonLED
//
//  Created by Michael Nation on 9/7/14.
//  Copyright (c) 2014 ArkonLED. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "UserInfoGlobal.h"
#import <CommonCrypto/CommonDigest.h>
#import "SSKeychain.h"
#import "SSKeychainQuery.h"

@interface Login : UITableViewController

@property (strong, nonatomic) IBOutlet UITextField *txtEmail;
@property (strong, nonatomic) IBOutlet UITextField *txtPassword;

@property (strong, nonatomic) IBOutlet UIButton *btnRememberMe;


@end
