//
//  MarkerInfoDelegate.m
//  ArkonLED
//
//  Created by Michael Nation on 9/9/14.
//  Copyright (c) 2014 ArkonLED. All rights reserved.
//

#import "MarkerInfoDelegate.h"

@interface MarkerInfoDelegate ()
{
    BOOL updatePhoto;
}

@property (strong, nonatomic) UserInfoGlobal *userInfoGlobal;

@end

@implementation MarkerInfoDelegate

- (id)initWithStyle:(UITableViewStyle)style
{
    self = [super initWithStyle:style];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    //Initialize Variables
    updatePhoto = NO;
    self.txtNumOfHeads.text = @"";
    self.txtWattage.text = @"";
    self.txtPoleHeight.text = @"";
    self.txtNumOfHeadsProposed.text = @"";
    self.userInfoGlobal = [[UserInfoGlobal alloc] init];
    
    self.hideSectionsWithHiddenRows = YES;
    
    //Change Content Mode for image button
    self.btnImage.imageView.contentMode = UIViewContentModeScaleAspectFit;
    [self.btnImage.imageView setClipsToBounds:YES];
    
    //New Marker
    if(self.marker.userData == NULL)
    {
        //Hide Delete button
        [self cell:self.cellDeleteMarker setHidden:YES];
        
        //Hide PrePopulate Switch if previous marker doesn't exist
        if(self.markerPrevious == NULL)
        {
            [self cell:self.cellPrepopulate setHidden:YES];
        }
    }
    //Existing Marker
    else
    {
        //Hide Cancel Button and Hide Prepopulate-Switch
        self.navigationItem.hidesBackButton = YES;
        self.navigationItem.leftBarButtonItem = nil;
        [self cell:self.cellPrepopulate setHidden:YES];
        
        self.dicMarkerGlobal = self.marker.userData;
        [self.segContAssemblyType setSelectedSegmentIndex:[[self.dicMarkerGlobal objectForKey:@"assemblyTypeID"] integerValue]];
        [self assemblyTypeChanged:self];
        
        [self.switchPoleExist setOn:[[self.dicMarkerGlobal objectForKey:@"poleExist"] boolValue] animated:NO];
        if(!self.switchPoleExist.isOn)
        {
            [self poleExistSwitchChanged:self];
        }
        self.txtNumOfHeads.text = [self.dicMarkerGlobal objectForKey:@"numOfHeads"];
        self.txtWattage.text = [self.dicMarkerGlobal objectForKey:@"wattage"];
        [self.switchOneToOne setOn:[[self.dicMarkerGlobal objectForKey:@"oneToOneReplace"] boolValue] animated:NO];
        if(!self.switchOneToOne.isOn)
        {
            [self oneToOneSwitchChanged:self];
        }
        self.txtNumOfHeadsProposed.text = [self.dicMarkerGlobal objectForKey:@"numOfHeadsProposed"];
        [self.switchBracket setOn:[[self.dicMarkerGlobal objectForKey:@"bracket"] boolValue] animated:NO];
        self.txtPoleHeight.text = [self.dicMarkerGlobal objectForKey:@"height"];
        
        /*if([[self.dicMarkerGlobal objectForKey:@"photo"] isEqualToString:@""])
        {
            self.btnImage.imageView.image = nil;
        }
        else
        {
            NSData *imageData = [NSData dataFromBase64String:[self.dicMarkerGlobal objectForKey:@"photo"]];
            [self.btnImage setImage:[UIImage imageWithData:imageData] forState:UIControlStateNormal];
        } */
        
        if([[self.dicMarkerGlobal objectForKey:@"hasPicture"] isEqualToString:@"1"])
        {
            NSDateFormatter* formatter = [[NSDateFormatter alloc] init] ;
            [formatter setDateFormat:@"yyyy-MM-dd_HH:MM:SS"];
            NSString* strDate = [formatter stringFromDate:[NSDate date]];
            
            NSURL* url = [NSURL URLWithString:[NSString stringWithFormat:@"http://ec2-54-165-80-46.compute-1.amazonaws.com/iOS/Images/%@.jpg?x=%@",[[self.dicMarkerGlobal objectForKey:@"poleID"] stringByAddingPercentEscapesUsingEncoding:NSUTF8StringEncoding], strDate]];
            NSURLRequest* request = [NSURLRequest requestWithURL:url];
            [NSURLConnection sendAsynchronousRequest:request queue:[NSOperationQueue mainQueue] completionHandler:^(NSURLResponse * response, NSData * data,NSError * error)
             {
                 if (!error)
                 {
                     [self.btnImage setImage:[UIImage imageWithData:data] forState:UIControlStateNormal];
                 }
                 else
                 {
                     UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"ERROR" message:[NSString stringWithFormat:@"%@",error] delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
                     
                     [errorView show];
                 }
                 
             }];
        }
        else
        {
            self.btnImage.imageView.image = nil;
        }
        
    }
    
    //Populate PickerView Bulb Types (Legacy Fixtures)
    NSURL* url = [NSURL URLWithString:@"http://ec2-54-165-80-46.compute-1.amazonaws.com/iOS/LightPoles/getLegacyFixtures.php"];
    NSURLRequest* request = [NSURLRequest requestWithURL:url];
    [NSURLConnection sendAsynchronousRequest:request queue:[NSOperationQueue mainQueue] completionHandler:^(NSURLResponse * response, NSData * data,NSError * error)
     {
         if (!error)
         {
             self.arrayBulbTypes = [NSJSONSerialization JSONObjectWithData:data options:NSJSONReadingMutableContainers error:nil];
             
             //New Marker
             if(self.dicMarkerGlobal == NULL)
             {
                 self.strSelectedBulbID = [[self.arrayBulbTypes objectAtIndex:0] objectForKey:@"bulb_ID"];
                 self.strSelectedBulbType = [[self.arrayBulbTypes objectAtIndex:0] objectForKey:@"bulb_description"];
                 
                 [self.pickerBulbType reloadAllComponents];
             }
             else
             //Existing Marker
             {
                 self.strSelectedBulbID = [self.dicMarkerGlobal objectForKey:@"bulbTypeID"];
                 self.strSelectedBulbType = [self.dicMarkerGlobal objectForKey:@"bulbTypeName"];
                 
                 //Find index of LED Fixture
                 for(int i = 0; i < self.arrayBulbTypes.count; i++)
                 {
                     NSDictionary *dicLEDFixture = [self.arrayBulbTypes objectAtIndex:i];
                     if([[dicLEDFixture objectForKey:@"bulb_ID"] isEqualToString:[self.dicMarkerGlobal objectForKey:@"bulbTypeID"]])
                     {
                         [self.pickerBulbType reloadAllComponents];
                         
                         [self.pickerBulbType selectRow:i inComponent:0 animated:NO];
                         break;
                     }
                 }
                 
             }
         }
         else
         {
             UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"ERROR" message:[NSString stringWithFormat:@"%@",error] delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
             
             [errorView show];
         }
     }];
    
    //Populate PickerView LED Fixtures
    NSURL* url2 = [NSURL URLWithString:@"http://ec2-54-165-80-46.compute-1.amazonaws.com/iOS/LightPoles/getLEDFixtures.php"];
    NSURLRequest* request2 = [NSURLRequest requestWithURL:url2];
    [NSURLConnection sendAsynchronousRequest:request2 queue:[NSOperationQueue mainQueue] completionHandler:^(NSURLResponse * response, NSData * data,NSError * error)
     {
         if (!error)
         {
             NSDictionary *dicServerMessage = [NSJSONSerialization JSONObjectWithData:data options:NSJSONReadingMutableContainers error:nil];
             
             //Get LED Fixtures Successful
             if([[dicServerMessage objectForKey:@"success"] isEqualToNumber:@1])
             {
                 self.arrayShoebox = [dicServerMessage objectForKey:@"shoebox"];
                 self.arrayWallpack = [dicServerMessage objectForKey:@"wallpack"];
                 
                 //New Marker
                 if(self.dicMarkerGlobal == NULL)
                 {
                     self.arrayLEDFixtures = self.arrayShoebox;
                     
                     self.strSelectedLEDFixtureID = [[self.arrayLEDFixtures objectAtIndex:0] objectForKey:@"LED_fixture_ID"];
                     
                     [self.pickerLEDFixtures reloadAllComponents];
                 }
                 else
                 //Existing Marker
                 {
                     //Showbox
                     if(self.segContAssemblyType.selectedSegmentIndex == 0)
                     {
                         self.arrayLEDFixtures = self.arrayShoebox;
                     }
                     //Wallpack
                     else
                     {
                         self.arrayLEDFixtures = self.arrayWallpack;
                     }
                     
                     self.strSelectedLEDFixtureID = [self.dicMarkerGlobal objectForKey:@"LEDFixtureID"];
                     
                     //Find index of LED Fixture
                     for(int i = 0; i < self.arrayLEDFixtures.count; i++)
                     {
                         NSDictionary *dicLEDFixture = [self.arrayLEDFixtures objectAtIndex:i];
                         if([[dicLEDFixture objectForKey:@"LED_fixture_ID"] isEqualToString:[self.dicMarkerGlobal objectForKey:@"LEDFixtureID"]])
                         {
                             [self.pickerLEDFixtures reloadAllComponents];
                             
                             [self.pickerLEDFixtures selectRow:i inComponent:0 animated:NO];
                             break;
                         }
                     }
                     
                 }
             }
             //Failed
             else
             {
                 UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"WARNING" message:[dicServerMessage objectForKey:@"message"] delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
                 
                 [errorView show];
             }
         }
         else
         {
             UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"ERROR" message:[NSString stringWithFormat:@"%@",error] delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
             
             [errorView show];
         }
     }];
    
    
    //Hides data selected datacells
    [self reloadDataAnimated:YES];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (IBAction)doneBtnClicked:(id)sender
{
    if(self.switchPoleExist.isOn && [self.txtNumOfHeads.text isEqualToString:@""])
    {
        UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"WARNING" message:@"Enter Number of Heads for Current Light Pole." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
        
        [errorView show];
        return;
    }
    
    if(self.switchPoleExist.isOn && [self.txtWattage.text isEqualToString:@""])
    {
        UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"WARNING" message:@"Enter Wattage for Current Light Pole." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
        
        [errorView show];
        return;
    }
    
    if(self.switchOneToOne.isOn && [self.txtNumOfHeadsProposed.text isEqualToString:@""])
    {
        UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"WARNING" message:@"Enter Number of Heads for Proposed Light Pole." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
        
        [errorView show];
        return;
    }
    
    NSMutableDictionary *dicMarker;
    BOOL newMarkerFlag = NO;
    
    NSString *strHasPicture = @"1";
    if(self.btnImage.imageView.image == nil)
    {
        strHasPicture = @"0";
    }
    
    //New Marker
    if(self.marker.userData == NULL)
    {
        dicMarker = [[NSMutableDictionary alloc] init];
        newMarkerFlag = YES;
        dicMarker[@"markerNum"] = self.markerCount;
    }
    //Existing Marker. Update Database
    else
    {
        dicMarker = self.marker.userData;
        
        NSString *strPoleHeight;
        if([self.txtPoleHeight.text isEqualToString:@""])
        {
            strPoleHeight = @"0";
        }
        else
        {
            strPoleHeight = self.txtPoleHeight.text;
        }
        
        /*
        NSString *myRequestString;
        if(updatePhoto)
        {
            NSString *imageData;
            if(self.btnImage.imageView.image == nil)
            {
                imageData = @"none";
            }
            else
            {
                //Compress image
                NSData *data = UIImageJPEGRepresentation(self.btnImage.imageView.image, 0);
                //Convert data to a string
                imageData = [data base64EncodedString];
                imageData = [imageData stringByReplacingOccurrencesOfString:@"+" withString:@"%2B"];
            }
            
            myRequestString = [NSString stringWithFormat:@"poleID=%@&poleExist=%@&numHeads=%@&bulbID=%@&assemblyTypeID=%@&legacyWattage=%@&picture=%@&oneToOneReplace=%@&numHeadsProposed=%@&poleHeight=%@&ledFixtureID=%@&bracket=%@&userID=%@", dicMarker[@"poleID"], @(self.switchPoleExist.isOn), self.txtNumOfHeads.text, self.strSelectedBulbID, @(self.segContAssemblyType.selectedSegmentIndex), self.txtWattage.text, imageData, @(self.switchOneToOne.isOn), self.txtNumOfHeadsProposed.text, strPoleHeight, self.strSelectedLEDFixtureID, @(self.switchBracket.isOn), [self.userInfoGlobal getUserID]];
        }
        else
        {
            myRequestString = [NSString stringWithFormat:@"poleID=%@&poleExist=%@&numHeads=%@&bulbID=%@&assemblyTypeID=%@&legacyWattage=%@&oneToOneReplace=%@&numHeadsProposed=%@&poleHeight=%@&ledFixtureID=%@&bracket=%@&userID=%@", dicMarker[@"poleID"], @(self.switchPoleExist.isOn), self.txtNumOfHeads.text, self.strSelectedBulbID, @(self.segContAssemblyType.selectedSegmentIndex), self.txtWattage.text, @(self.switchOneToOne.isOn), self.txtNumOfHeadsProposed.text, strPoleHeight, self.strSelectedLEDFixtureID, @(self.switchBracket.isOn), [self.userInfoGlobal getUserID]];
        } */
        
        if(updatePhoto)
        {
            //UPLOAD IMAGE TO URL HERE
            if(self.btnImage.imageView.image != nil)
            {
                [self uploadImage:UIImageJPEGRepresentation(self.btnImage.imageView.image, 0) filename:dicMarker[@"poleID"]];
            }
            //Delete image if image is nil and a picture in DB currently exist
            else if(self.btnImage.imageView.image == nil && [dicMarker[@"hasPicture"] isEqualToString:@"1"])
            {
                NSString *myRequestString = [NSString stringWithFormat:@"poleID=%@", dicMarker[@"poleID"]];
                
                // Create Data from request
                NSData *myRequestData = [NSData dataWithBytes:[myRequestString UTF8String] length:[myRequestString length]];
                NSMutableURLRequest *request = [[NSMutableURLRequest alloc] initWithURL:[NSURL URLWithString: @"http://ec2-54-165-80-46.compute-1.amazonaws.com/iOS/LightPoles/deletePicture.php"]];
                // set Request Type
                [request setHTTPMethod: @"POST"];
                // Set content-type
                [request setValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"content-type"];
                // Set Request Body
                [request setHTTPBody: myRequestData];
                // Now send a request and get Response
                [NSURLConnection sendAsynchronousRequest:request queue:[NSOperationQueue mainQueue] completionHandler:^(NSURLResponse * response, NSData * data,NSError * error)
                 {
                     //Delete Failed
                     if(error)
                     {
                         UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"ERROR" message:[NSString stringWithFormat:@"%@",error] delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
                         
                         [errorView show];
                         
                     }
                 }];
            }
        }
        
        NSString *myRequestString = [NSString stringWithFormat:@"poleID=%@&poleExist=%@&numHeads=%@&bulbID=%@&assemblyTypeID=%@&legacyWattage=%@&hasPicture=%@&oneToOneReplace=%@&numHeadsProposed=%@&poleHeight=%@&ledFixtureID=%@&bracket=%@&userID=%@", dicMarker[@"poleID"], @(self.switchPoleExist.isOn), self.txtNumOfHeads.text, self.strSelectedBulbID, @(self.segContAssemblyType.selectedSegmentIndex), self.txtWattage.text, strHasPicture, @(self.switchOneToOne.isOn), self.txtNumOfHeadsProposed.text, strPoleHeight, self.strSelectedLEDFixtureID, @(self.switchBracket.isOn), [self.userInfoGlobal getUserID]];
        
        // Create Data from request
        NSData *myRequestData = [NSData dataWithBytes:[myRequestString UTF8String] length:[myRequestString length]];
        NSMutableURLRequest *request = [[NSMutableURLRequest alloc] initWithURL:[NSURL URLWithString: @"http://ec2-54-165-80-46.compute-1.amazonaws.com/iOS/LightPoles/updatePole.php"]];
        // set Request Type
        [request setHTTPMethod: @"POST"];
        // Set content-type
        [request setValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"content-type"];
        // Set Request Body
        [request setHTTPBody: myRequestData];
        // Now send a request and get Response
        [NSURLConnection sendAsynchronousRequest:request queue:[NSOperationQueue mainQueue] completionHandler:^(NSURLResponse * response, NSData * data,NSError * error)
         {
             if (!error)
             {
                 NSDictionary *dicServerMessage = [NSJSONSerialization JSONObjectWithData:data options:NSJSONReadingMutableContainers error:nil];
                 
                 //Update Failed
                 if([[dicServerMessage objectForKey:@"success"] isEqualToNumber:@0])
                 {
                     UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"WARNING" message:[dicServerMessage objectForKey:@"message"] delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
                     
                     [errorView show];
                 }
             }
             else
             {
                 UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"ERROR" message:[NSString stringWithFormat:@"%@",error] delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
                 
                 [errorView show];
                 
             }
         }];
    }
    
    dicMarker[@"poleExist"] = @(self.switchPoleExist.isOn);
    dicMarker[@"numOfHeads"] = self.txtNumOfHeads.text;
    dicMarker[@"bulbTypeName"] = self.strSelectedBulbType;
    dicMarker[@"bulbTypeID"] = self.strSelectedBulbID;
    dicMarker[@"assemblyTypeID"] = @(self.segContAssemblyType.selectedSegmentIndex);
    dicMarker[@"wattage"] = self.txtWattage.text;
    dicMarker[@"oneToOneReplace"] = @(self.switchOneToOne.isOn);
    dicMarker[@"numOfHeadsProposed"] = self.txtNumOfHeadsProposed.text;
    dicMarker[@"LEDFixtureID"] = self.strSelectedLEDFixtureID;
    dicMarker[@"bracket"] = @(self.switchBracket.isOn);
    dicMarker[@"height"] = self.txtPoleHeight.text;
    dicMarker[@"hasPicture"] = strHasPicture;
    if([strHasPicture isEqualToString:@"1"])
    {
        dicMarker[@"pictureData"] = UIImageJPEGRepresentation(self.btnImage.imageView.image, 0);
    }
    else
    {
        dicMarker[@"pictureData"] = @"";
    }
    
    /*
    if(self.btnImage.imageView.image == nil)
    {
        dicMarker[@"photo"] = @"";
    }
    else if(updatePhoto)
    {
        //Compress image
        NSData *data = UIImageJPEGRepresentation(self.btnImage.imageView.image, 0);
        //Convert data to a string
        NSString *imageData = [data base64EncodedString];
        dicMarker[@"photo"] = [imageData stringByReplacingOccurrencesOfString:@"+" withString:@"%2B"];
    } */
    
    
    self.marker.userData = dicMarker;
    self.marker.title = [NSString stringWithFormat:@"#%@", dicMarker[@"markerNum"]];
    self.marker.snippet = @"";
    //Current
    if(self.switchPoleExist.isOn)
    {
        if([self.txtNumOfHeads.text intValue] == 1)
        {
            //Shoebox
            if(self.segContAssemblyType.selectedSegmentIndex == 0)
            {
                self.marker.snippet = [NSString stringWithFormat:@"C: %@ Shoebox, %@W %@\n", self.txtNumOfHeads.text, self.txtWattage.text, self.strSelectedBulbType];
            }
            //Wallpack
            else
            {
                self.marker.snippet = [NSString stringWithFormat:@"C: %@ Wallpack, %@W %@\n", self.txtNumOfHeads.text, self.txtWattage.text, self.strSelectedBulbType];
            }
        }
        else
        {
            //Shoebox
            if(self.segContAssemblyType.selectedSegmentIndex == 0)
            {
                self.marker.snippet = [NSString stringWithFormat:@"C: %@ Shoeboxes, %@W %@\n", self.txtNumOfHeads.text, self.txtWattage.text, self.strSelectedBulbType];
            }
            //Wallpack
            else
            {
                self.marker.snippet = [NSString stringWithFormat:@"C: %@ Wallpacks, %@W %@\n", self.txtNumOfHeads.text, self.txtWattage.text, self.strSelectedBulbType];
            }
        }
    }
    
    //Proposed
    if(self.switchOneToOne.isOn)
    {
        //Find index of LED Fixture. Get Wattage
        NSString *strLEDFixtureWattage = @"";
        for(int i = 0; i < self.arrayLEDFixtures.count; i++)
        {
            NSDictionary *dicLEDFixture = [self.arrayLEDFixtures objectAtIndex:i];
            if([[dicLEDFixture objectForKey:@"LED_fixture_ID"] isEqualToString:self.strSelectedLEDFixtureID])
            {
                strLEDFixtureWattage = [dicLEDFixture objectForKey:@"LED_wattage"];
                break;
            }
        }
        
        if([self.txtNumOfHeadsProposed.text intValue] == 1)
        {
            //Shoebox
            if(self.segContAssemblyType.selectedSegmentIndex == 0)
            {
                self.marker.snippet = [NSString stringWithFormat:@"%@P: %@ Shoebox, %@W LED", self.marker.snippet, self.txtNumOfHeadsProposed.text, strLEDFixtureWattage];
            }
            //Wallpack
            else
            {
                self.marker.snippet = [NSString stringWithFormat:@"%@P: %@ Wallpack, %@W LED", self.marker.snippet, self.txtNumOfHeadsProposed.text, strLEDFixtureWattage];
            }
        }
        else
        {
            //Shoebox
            if(self.segContAssemblyType.selectedSegmentIndex == 0)
            {
                self.marker.snippet = [NSString stringWithFormat:@"%@P: %@ Shoeboxes, %@W LED", self.marker.snippet, self.txtNumOfHeadsProposed.text, strLEDFixtureWattage];
            }
            //Wallpack
            else
            {
                self.marker.snippet = [NSString stringWithFormat:@"%@P: %@ Wallpacks, %@W LED", self.marker.snippet, self.txtNumOfHeadsProposed.text, strLEDFixtureWattage];
            }
        }
    }
    
    //Return to Map
    [self.delegate doneMarkerInfo:self.marker isNewMarker:newMarkerFlag];
}

- (IBAction)cancelBtnClicked:(id)sender
{
    self.marker.userData = NULL;
    [self.delegate doneMarkerInfo:self.marker isNewMarker:YES];
}

- (IBAction)deleteBtnClicked:(id)sender
{
    //Display message asking user if they are sure they want to delete
    UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"CONFIRM" message:[NSString stringWithFormat:@"Are you sure you want to delete this light pole?"] delegate:self cancelButtonTitle:@"Cancel" otherButtonTitles:@"Delete", nil];
    [alert show];
}

-(void) alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex
{
    
    //Delete was clicked
    if(buttonIndex == 1)
    {
        NSDictionary *dicMarker = self.marker.userData;
        
        NSString *myRequestString = [NSString stringWithFormat:@"poleID=%@&hasPicture=%@&userID=%@", dicMarker[@"poleID"], dicMarker[@"hasPicture"],[self.userInfoGlobal getUserID]];
        
        // Create Data from request
        NSData *myRequestData = [NSData dataWithBytes:[myRequestString UTF8String] length:[myRequestString length]];
        NSMutableURLRequest *request = [[NSMutableURLRequest alloc] initWithURL:[NSURL URLWithString: @"http://ec2-54-165-80-46.compute-1.amazonaws.com/iOS/LightPoles/deletePole.php"]];
        // set Request Type
        [request setHTTPMethod:@"POST"];
        // Set content-type
        [request setValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"content-type"];
        // Set Request Body
        [request setHTTPBody: myRequestData];
        // Now send a request and get Response
        [NSURLConnection sendAsynchronousRequest:request queue:[NSOperationQueue mainQueue] completionHandler:^(NSURLResponse * response, NSData * data,NSError * error)
         {
             if (!error)
             {
                 NSDictionary *dicServerMessage = [NSJSONSerialization JSONObjectWithData:data options:NSJSONReadingMutableContainers error:nil];
                 
                 //Delete failed
                 if([[dicServerMessage objectForKey:@"success"] isEqualToNumber:@0])
                 {
                     UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"WARNING" message:[dicServerMessage objectForKey:@"message"] delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
                     
                     [errorView show];
                 }
             }
             else
             {
                 UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"ERROR" message:[NSString stringWithFormat:@"%@",error] delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
                 
                 [errorView show];
                 
             }
         }];
        
        self.marker.userData = NULL;
        [self.delegate doneMarkerInfo:self.marker isNewMarker:NO];
    }
}

- (IBAction)numOfHeadsCurrentChanged:(id)sender
{
    if(![self.txtNumOfHeads.text isEqualToString:@""])
    {
        self.txtNumOfHeadsProposed.text = self.txtNumOfHeads.text;
    }
}


- (IBAction)assemblyTypeChanged:(id)sender
{
    //Shoebox
    if(self.segContAssemblyType.selectedSegmentIndex == 0)
    {
        self.arrayLEDFixtures = self.arrayShoebox;
    }
    //Wallpack
    else
    {
        self.arrayLEDFixtures = self.arrayWallpack;
        
        //Set Number of Heads to 1
        self.txtNumOfHeads.text = @"1";
        self.txtNumOfHeadsProposed.text = @"1";
    }
    
    [self.pickerLEDFixtures reloadAllComponents];
    
    //Default to first item when assembly type changes
    self.strSelectedLEDFixtureID = [[self.arrayLEDFixtures objectAtIndex:0] objectForKey:@"LED_fixture_ID"];
    [self.pickerLEDFixtures selectRow:0 inComponent:0 animated:NO];
}


- (IBAction)prepopulateSwitchChanged:(id)sender
{
    if(self.switchPrepopulate.isOn)
    {
        NSDictionary *dicMarker = self.markerPrevious.userData;
        self.segContAssemblyType.selectedSegmentIndex = [dicMarker[@"assemblyTypeID"] longValue];
        [self assemblyTypeChanged:self];
        
        [self.switchPoleExist setOn:[[dicMarker objectForKey:@"poleExist"] boolValue] animated:NO];
        [self poleExistSwitchChanged:self];
        
        self.txtNumOfHeads.text = [dicMarker objectForKey:@"numOfHeads"];
        self.txtWattage.text = [dicMarker objectForKey:@"wattage"];
        //Find index of Bulb Type
        for(int i = 0; i < self.arrayBulbTypes.count; i++)
        {
            NSDictionary *dicLEDFixture = [self.arrayBulbTypes objectAtIndex:i];
            if([[dicLEDFixture objectForKey:@"bulb_ID"] isEqualToString:[dicMarker objectForKey:@"bulbTypeID"]])
            {
                self.strSelectedBulbID = [dicMarker objectForKey:@"bulbTypeID"];
                self.strSelectedBulbType = [dicMarker objectForKey:@"bulbTypeName"];
                
                [self.pickerBulbType reloadAllComponents];
                
                [self.pickerBulbType selectRow:i inComponent:0 animated:NO];
                break;
            }
        }
        
        //Proposed************************
        //Find index of LED Fixture
        for(int i = 0; i < self.arrayLEDFixtures.count; i++)
        {
            NSDictionary *dicLEDFixture = [self.arrayLEDFixtures objectAtIndex:i];
            if([[dicLEDFixture objectForKey:@"LED_fixture_ID"] isEqualToString:[dicMarker objectForKey:@"LEDFixtureID"]])
            {
                self.strSelectedLEDFixtureID = [dicMarker objectForKey:@"LEDFixtureID"];
                
                [self.pickerLEDFixtures reloadAllComponents];
                
                [self.pickerLEDFixtures selectRow:i inComponent:0 animated:NO];
                break;
            }
        }
        [self.switchOneToOne setOn:[[dicMarker objectForKey:@"oneToOneReplace"] boolValue] animated:NO];
        [self oneToOneSwitchChanged:self];
        
        self.txtNumOfHeadsProposed.text = [dicMarker objectForKey:@"numOfHeadsProposed"];
        
        [self.switchBracket setOn:[[dicMarker objectForKey:@"bracket"] boolValue] animated:NO];
        self.txtPoleHeight.text = [dicMarker objectForKey:@"height"];
    }
}

- (IBAction)poleExistSwitchChanged:(id)sender
{
    if(self.switchPoleExist.isOn)
    {
        [self cell:self.cellNumOfHeads setHidden:NO];
        [self cell:self.cellWattage setHidden:NO];
        [self cell:self.cellBulbTypes setHidden:NO];
    }
    else
    {
        [self cell:self.cellNumOfHeads setHidden:YES];
        [self cell:self.cellWattage setHidden:YES];
        [self cell:self.cellBulbTypes setHidden:YES];
    }
    
    //Hides data selected datacells
    [self reloadDataAnimated:YES];
}


- (IBAction)oneToOneSwitchChanged:(id)sender
{
    if(self.switchOneToOne.isOn)
    {
        [self cell:self.cellNumOfHeadsProposed setHidden:NO];
        [self cell:self.cellModalTypes setHidden:NO];
        [self cell:self.cellBracket setHidden:NO];
        [self cell:self.cellPoleHeight setHidden:NO];
        [self cell:self.cellImage setHidden:NO];
    }
    else
    {
        [self cell:self.cellNumOfHeadsProposed setHidden:YES];
        [self cell:self.cellModalTypes setHidden:YES];
        [self cell:self.cellBracket setHidden:YES];
        [self cell:self.cellPoleHeight setHidden:YES];
        [self cell:self.cellImage setHidden:YES];
    }
    
    //Hides data selected datacells
    [self reloadDataAnimated:YES];
}


- (IBAction)choosePhotoBtnClicked:(id)sender
{
    updatePhoto = YES;
    
    UIImagePickerController *picker = [[UIImagePickerController alloc] init];
    picker.sourceType = UIImagePickerControllerSourceTypeCamera;
    picker.delegate = self;
    [self presentViewController:picker animated:YES completion:nil];
}

- (void)imagePickerController:(UIImagePickerController *)picker
        didFinishPickingImage:(UIImage *)image
                  editingInfo:(NSDictionary *)editingInfo
{
    
    [self.btnImage setImage:image forState:UIControlStateNormal];
    
    [[picker presentingViewController] dismissViewControllerAnimated:YES completion:nil];
}

- (IBAction)removeImageBtnClicked:(id)sender
{
    updatePhoto = YES;
    self.btnImage.imageView.image = nil;
}

- (void)uploadImage:(NSData *)imageData filename:(NSString *)filename
{
    NSString *urlString = @"http://ec2-54-165-80-46.compute-1.amazonaws.com/iOS/LightPoles/uploadPicture.php";
    
    NSMutableURLRequest *request = [[NSMutableURLRequest alloc] init];
    [request setURL:[NSURL URLWithString:urlString]];
    [request setHTTPMethod:@"POST"];
    NSString *boundary = @"---------------------------14737809831466499882746641449";
    NSString *contentType = [NSString stringWithFormat:@"multipart/form-data; boundary=%@",boundary];
    [request addValue:contentType forHTTPHeaderField: @"Content-Type"];
    
    NSMutableData *body = [NSMutableData data];
    [body appendData:[[NSString stringWithFormat:@"\r\n--%@\r\n",boundary] dataUsingEncoding:NSUTF8StringEncoding]];
    [body appendData:[[NSString stringWithString:[NSString stringWithFormat:@"Content-Disposition: form-data; name=\"userfile\"; filename=\"%@\"\r\n",filename]] dataUsingEncoding:NSUTF8StringEncoding]];
    [body appendData:[@"Content-Type: application/octet-stream\r\n\r\n" dataUsingEncoding:NSUTF8StringEncoding]];
    [body appendData:[NSData dataWithData:imageData]];
    [body appendData:[[NSString stringWithFormat:@"\r\n--%@--\r\n",boundary] dataUsingEncoding:NSUTF8StringEncoding]];
    [request setHTTPBody:body];
    [NSURLConnection sendAsynchronousRequest:request queue:[NSOperationQueue mainQueue] completionHandler:^(NSURLResponse *response, NSData *data, NSError *error)
     {
         if (!error)
         {
             NSDictionary *dicServerMessage = [NSJSONSerialization JSONObjectWithData:data options:NSJSONReadingMutableContainers error:nil];
             
             //Delete failed
             if([[dicServerMessage objectForKey:@"success"] isEqualToNumber:@0])
             {
                 UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"WARNING" message:[dicServerMessage objectForKey:@"message"] delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
                 
                 [errorView show];
             }
         }
         else
         {
             UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"ERROR" message:[NSString stringWithFormat:@"%@",error] delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
             
             [errorView show];
             
         }
     }];
}

- (IBAction)btnImageClicked:(id)sender
{
    if(self.btnImage.imageView.image != nil)
    {
        [self performSegueWithIdentifier:@"ToLargeImage" sender:self];
    }
}


//PickerView Delegate Methods*********************************************************
- (void)pickerView:(UIPickerView *)pickerView didSelectRow:(NSInteger)row inComponent:(NSInteger)component
{
    if(pickerView.tag == 0)
    {
        self.strSelectedBulbType = [[self.arrayBulbTypes objectAtIndex:row] objectForKey:@"bulb_description"];
        self.strSelectedBulbID = [[self.arrayBulbTypes objectAtIndex:row] objectForKey:@"bulb_ID"];
    }
    else
    {
        self.strSelectedLEDFixtureID = [[self.arrayLEDFixtures objectAtIndex:row] objectForKey:@"LED_fixture_ID"];
    }
}

- (NSInteger)numberOfComponentsInPickerView:(UIPickerView *)pickerView
{
    return 1;
}

- (NSInteger)pickerView:(UIPickerView *)pickerView numberOfRowsInComponent:(NSInteger)component
{
    if(pickerView.tag == 0)
    {
        return self.arrayBulbTypes.count;
    }
    else
    {
        return self.arrayLEDFixtures.count;
    }
}

// tell the picker the title for a given component
- (NSString *)pickerView:(UIPickerView *)pickerView titleForRow:(NSInteger)row forComponent:(NSInteger)component
{
    if(pickerView.tag == 0)
    {
        return [[self.arrayBulbTypes objectAtIndex:row] objectForKey:@"bulb_description"];
    }
    else
    {
        return [[self.arrayLEDFixtures objectAtIndex:row] objectForKey:@"LED_fixture_description"];
    }
}

- (IBAction)dismissKeyboard:(id)sender
{
    [self.view endEditing:YES];
}

//Tableview Delegate Methods **********************************************************
-(void)tableView:(UITableView *)tableView willDisplayCell:(UITableViewCell *)cell forRowAtIndexPath:(NSIndexPath *)indexPath
{
    if ([cell respondsToSelector:@selector(setSeparatorInset:)]) {
        [cell setSeparatorInset:UIEdgeInsetsMake(0, 8, 0, 0)];
    }
    
    if ([cell respondsToSelector:@selector(setLayoutMargins:)]) {
        [cell setLayoutMargins:UIEdgeInsetsMake(0, 8, 0, 0)];
    }
}

-(void)viewDidLayoutSubviews
{
    if ([self.tableView respondsToSelector:@selector(setSeparatorInset:)]) {
        [self.tableView setSeparatorInset:UIEdgeInsetsMake(0, 8, 0, 0)];
    }
    
    if ([self.tableView respondsToSelector:@selector(setLayoutMargins:)]) {
        [self.tableView setLayoutMargins:UIEdgeInsetsMake(0, 8, 0, 0)];
    }
}
//Tableview Delegate Methods DONE ******************************************************


- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender
{
    if([segue.identifier isEqualToString:@"ToLargeImage"])
    {
        LightPoleLargeImage *vc = segue.destinationViewController;
        vc.imgLightPole = self.btnImage.imageView.image;
    }
}
@end
