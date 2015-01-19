//
//  Presentation.h
//  SiteLite
//
//  Created by Michael Nation on 11/14/14.
//  Copyright (c) 2014 ArkonLED. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface Presentation : UIViewController

@property (strong, nonatomic) IBOutlet UIWebView *webView;

@property (strong, nonatomic) NSString *strProjectID;
@property (strong, nonatomic) NSString *strProjectTitle;
@end
