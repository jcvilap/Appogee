//
//  LightPoleLargeImage.m
//  SiteLite
//
//  Created by Michael Nation on 11/2/14.
//  Copyright (c) 2014 ArkonLED. All rights reserved.
//

#import "LightPoleLargeImage.h"

@interface LightPoleLargeImage ()

@end

@implementation LightPoleLargeImage

- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view.
    
    self.imageView.image = self.imgLightPole;
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (IBAction)btnBackClicked:(id)sender
{
    //Show Nav bar
    [self.navigationController setNavigationBarHidden:NO animated:NO];
    //Show Status Bar
    [[UIApplication sharedApplication] setStatusBarHidden:NO withAnimation:NO];
    
    [self.navigationController popViewControllerAnimated:YES];
}

- (IBAction)toggleNavBar:(id)sender
{
    //Hide Nav bar
    [self.navigationController setNavigationBarHidden:!self.navigationController.navigationBarHidden animated:YES];
    //Hide Status Bar
    [[UIApplication sharedApplication] setStatusBarHidden:self.navigationController.navigationBarHidden withAnimation:YES];
}

//Hide Status Bar
- (BOOL)prefersStatusBarHidden
{
    return self.navigationController.navigationBarHidden;
}

@end
