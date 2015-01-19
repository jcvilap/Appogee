//
//  ProjectsList.m
//  ArkonLED
//
//  Created by Michael Nation on 9/7/14.
//  Copyright (c) 2014 ArkonLED. All rights reserved.
//

#import "ProjectsList.h"

@interface ProjectsList ()

@property (strong, nonatomic) UserInfoGlobal *userInfoGlobal;

@end

@implementation ProjectsList

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
    
    //Display Nav Bar
    [self.navigationController setNavigationBarHidden:NO];
    
    //initialize
    self.userInfoGlobal = [[UserInfoGlobal alloc] init];
    self.strAssemblyType = @"ACTIVE";
    
    //Hide seperator lines for empty cells
    self.tableView.tableFooterView = [[UIView alloc] initWithFrame:CGRectZero];
    
}

- (void)viewWillAppear:(BOOL)animated
{
    self.navigationController.toolbarHidden = NO;
    
    //Get Projects
    NSString *strURL = [NSString stringWithFormat:@"http://ec2-54-165-80-46.compute-1.amazonaws.com/iOS/Projects/getProjectNamesByStatus.php?userID=%@&userType=%@", [self.userInfoGlobal getUserID], [self.userInfoGlobal getUserType]];
    
    // Create Data from request
    NSMutableURLRequest *request = [[NSMutableURLRequest alloc] initWithURL:[NSURL URLWithString:strURL]];
    // set Request Type
    [request setHTTPMethod: @"GET"];
    // Set content-type
    [request setValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"content-type"];
    // Now send a request and get Response
    [NSURLConnection sendAsynchronousRequest:request queue:[NSOperationQueue mainQueue] completionHandler:^(NSURLResponse * response, NSData * data,NSError * error)
     {
         if (!error)
         {
             NSDictionary *dicServerMessage = [NSJSONSerialization JSONObjectWithData:data options:NSJSONReadingMutableContainers error:nil];
             
             //Successful
             if([[dicServerMessage objectForKey:@"success"] isEqualToNumber:@1])
             {
                 self.activeProjects = [dicServerMessage objectForKey:@"active"];
                 self.inactiveProjects = [dicServerMessage objectForKey:@"inactive"];
                 self.closedProjects = [dicServerMessage objectForKey:@"closed"];
                 
                 [self.tableView reloadData];
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
    
    //Get Info For Email
    NSString *strURL2 = [NSString stringWithFormat:@"http://ec2-54-165-80-46.compute-1.amazonaws.com/iOS/Projects/getCostsAndAssumptions.php?userID=%@", [self.userInfoGlobal getUserID]];
    
    // Create Data from request
    NSMutableURLRequest *request2 = [[NSMutableURLRequest alloc] initWithURL:[NSURL URLWithString:strURL2]];
    // set Request Type
    [request2 setHTTPMethod: @"GET"];
    // Set content-type
    [request2 setValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"content-type"];
    // Now send a request and get Response
    [NSURLConnection sendAsynchronousRequest:request2 queue:[NSOperationQueue mainQueue] completionHandler:^(NSURLResponse * response, NSData * data,NSError * error)
     {
         if (!error)
         {
             self.dicEmailClient = [NSJSONSerialization JSONObjectWithData:data options:NSJSONReadingMutableContainers error:nil];
             
             ////Failed
             if([[self.dicEmailClient objectForKey:@"success"] isEqualToNumber:@0])
             {
                 UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"WARNING" message:[self.dicEmailClient objectForKey:@"message"] delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
                 
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

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (IBAction)profileBtnClicked:(id)sender
{
    //Hide nav bar
    [self.navigationController setNavigationBarHidden:YES animated:NO];
    //Hide Tool bar
    self.navigationController.toolbarHidden = YES;
    
    //Delete keychain if exist
    if([SSKeychain passwordForService:@"arkonled" account:@"email"] != nil)
    {
        [SSKeychain deletePasswordForService:@"arkonled" account:@"email"];
        [SSKeychain deletePasswordForService:@"arkonled" account:@"password"];
    }
    
    [self.navigationController popViewControllerAnimated:YES];
}

- (IBAction)createNewProject:(id)sender
{
    [self performSegueWithIdentifier:@"NewProjectToMapNew" sender:self];
}


//TableView Methods*************************************************************************
-(void)tableView:(UITableView *)tableView willDisplayCell:(UITableViewCell *)cell forRowAtIndexPath:(NSIndexPath *)indexPath
{
    if ([cell respondsToSelector:@selector(setSeparatorInset:)]) {
        [cell setSeparatorInset:UIEdgeInsetsMake(0, 8, 0, 0)];
    }
    
    if ([cell respondsToSelector:@selector(setLayoutMargins:)]) {
        [cell setLayoutMargins:UIEdgeInsetsMake(0, 8, 0, 0)];
    }
}

-(CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath*)indexPath
{
    return 105;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    if([self.strAssemblyType isEqualToString:@"ACTIVE"])
    {
        return self.activeProjects.count;
    }
    else if([self.strAssemblyType isEqualToString:@"INACTIVE"])
    {
        return self.inactiveProjects.count;
    }
    else if([self.strAssemblyType isEqualToString:@"CLOSED"])
    {
        return self.closedProjects.count;
    }
    //ERROR
    else
    {
        return 0;
    }
    
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    static NSString *CellIdentifier = @"Cell";
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CellIdentifier forIndexPath:indexPath];
    
    NSDictionary *dicProjectInfo;
    if([self.strAssemblyType isEqualToString:@"ACTIVE"])
    {
        dicProjectInfo = [self.activeProjects objectAtIndex:indexPath.row];
    }
    else if([self.strAssemblyType isEqualToString:@"INACTIVE"])
    {
        dicProjectInfo = [self.inactiveProjects objectAtIndex:indexPath.row];
    }
    else if([self.strAssemblyType isEqualToString:@"CLOSED"])
    {
        dicProjectInfo = [self.closedProjects objectAtIndex:indexPath.row];
    }
    //ERROR
    else
    {
        dicProjectInfo = nil;
    }
    
    UILabel *lblTitle = (UILabel *)[cell viewWithTag:1];
    lblTitle.text = [dicProjectInfo objectForKey:@"project_name"];
    
    NSDateFormatter *formatter = [[NSDateFormatter alloc] init];
    [formatter setDateFormat:@"yyyy-MM-dd HH:mm:ss"];
    [formatter setTimeZone:[NSTimeZone timeZoneWithAbbreviation:@"UTC"]];
    NSDate *date = [formatter dateFromString:[dicProjectInfo objectForKey:@"date_opened"]];
    [formatter setDateStyle:NSDateFormatterShortStyle];
    //[formatter setTimeStyle:NSDateFormatterShortStyle];
    [formatter setTimeZone:[NSTimeZone systemTimeZone]];
    UILabel *lblDateOpen = (UILabel *)[cell viewWithTag:2];
    lblDateOpen.text = [formatter stringFromDate:date];
    
    UILabel *lblName = (UILabel *)[cell viewWithTag:3];
    if([[self.userInfoGlobal getUserType] isEqualToString:@"3"]) //Sales Rep, display Contact Name
    {
        lblName.text = [dicProjectInfo objectForKey:@"contact_name"];
    }
    else //Admin, display Salesman's Name
    {
        lblName.text = [NSString stringWithFormat:@"%@ %@", [dicProjectInfo objectForKey:@"first_name"], [dicProjectInfo objectForKey:@"last_name"]];
    }
    
    UILabel *lblLocation = (UILabel *)[cell viewWithTag:6];
    if(![[dicProjectInfo objectForKey:@"city"] isEqualToString:@""])
    {
        lblLocation.text = [NSString stringWithFormat:@"%@, %@",[dicProjectInfo objectForKey:@"city"], [dicProjectInfo objectForKey:@"state"]];
    }
    else
    {
        lblLocation.text = @"";
    }
    
    return cell;
}

- (BOOL)tableView:(UITableView *)tableView canEditRowAtIndexPath:(NSIndexPath *)indexPath
{
    // Return NO if you do not want the specified item to be editable.
    return YES;
}

- (void)tableView:(UITableView *)tableView commitEditingStyle:(UITableViewCellEditingStyle)editingStyle forRowAtIndexPath:(NSIndexPath *)indexPath
{
    if (editingStyle == UITableViewCellEditingStyleDelete)
    {
        NSDictionary *dicProjectInfo;
        // Remove from array
        if([self.strAssemblyType isEqualToString:@"ACTIVE"])
        {
            dicProjectInfo = [self.activeProjects objectAtIndex:indexPath.row];
            [self.activeProjects removeObjectAtIndex:indexPath.row];
        }
        else if([self.strAssemblyType isEqualToString:@"INACTIVE"])
        {
            dicProjectInfo = [self.inactiveProjects objectAtIndex:indexPath.row];
            [self.inactiveProjects removeObjectAtIndex:indexPath.row];
        }
        else if([self.strAssemblyType isEqualToString:@"CLOSED"])
        {
            dicProjectInfo = [self.closedProjects objectAtIndex:indexPath.row];
            [self.closedProjects removeObjectAtIndex:indexPath.row];
        }
        
        // This line manages to delete the cell on tableView
        [tableView deleteRowsAtIndexPaths:@[indexPath] withRowAnimation:UITableViewRowAnimationFade];
        
        NSString *myRequestString = [NSString stringWithFormat:@"projectID=%@&userID=%@", dicProjectInfo[@"project_ID"], [self.userInfoGlobal getUserID]];
        
        // Create Data from request
        NSData *myRequestData = [NSData dataWithBytes:[myRequestString UTF8String] length:[myRequestString length]];
        NSMutableURLRequest *request = [[NSMutableURLRequest alloc] initWithURL:[NSURL URLWithString: @"http://ec2-54-165-80-46.compute-1.amazonaws.com/iOS/Projects/deleteProject.php"]];
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
    }
}
//TableView Methods DONE*************************************************************************


//Status Types
- (IBAction)btnActiveClicked:(id)sender
{
    self.strAssemblyType = @"ACTIVE";
    self.btnActive.tintColor = [UIColor darkGrayColor];
    self.btnInactive.tintColor = [[[[UIApplication sharedApplication] delegate] window] tintColor];
    self.btnClosed.tintColor = [[[[UIApplication sharedApplication] delegate] window] tintColor];
    
    [self.tableView reloadData];
}

- (IBAction)btnInactiveClicked:(id)sender
{
    self.strAssemblyType = @"INACTIVE";
    self.btnActive.tintColor = [[[[UIApplication sharedApplication] delegate] window] tintColor];
    self.btnInactive.tintColor = [UIColor darkGrayColor];
    self.btnClosed.tintColor = [[[[UIApplication sharedApplication] delegate] window] tintColor];
    
    [self.tableView reloadData];
}

- (IBAction)btnClosedClicked:(id)sender
{
    self.strAssemblyType = @"CLOSED";
    self.btnActive.tintColor = [[[[UIApplication sharedApplication] delegate] window] tintColor];
    self.btnInactive.tintColor = [[[[UIApplication sharedApplication] delegate] window] tintColor];
    self.btnClosed.tintColor = [UIColor darkGrayColor];
    
    [self.tableView reloadData];
}

- (IBAction)btnEmailClient:(id)sender
{
    CGPoint buttonPosition = [sender convertPoint:CGPointZero toView:self.tableView];
    NSIndexPath *indexPath = [self.tableView indexPathForRowAtPoint:buttonPosition];
    
    NSDictionary *dicProjectInfo;
    if([self.strAssemblyType isEqualToString:@"ACTIVE"])
    {
        dicProjectInfo = [self.activeProjects objectAtIndex:indexPath.row];
    }
    else if([self.strAssemblyType isEqualToString:@"INACTIVE"])
    {
        dicProjectInfo = [self.inactiveProjects objectAtIndex:indexPath.row];
    }
    else if([self.strAssemblyType isEqualToString:@"CLOSED"])
    {
        dicProjectInfo = [self.closedProjects objectAtIndex:indexPath.row];
    }
    //ERROR
    else
    {
        dicProjectInfo = nil;
    }
    
    MFMailComposeViewController *mailComposer = [[MFMailComposeViewController alloc] init];
    mailComposer.mailComposeDelegate = self;
    
    //show_email must be 1
    NSMutableArray *emailAddressesArray = [[NSMutableArray alloc] init];
    [emailAddressesArray addObject:dicProjectInfo[@"contact_email"]];
    [mailComposer setToRecipients:emailAddressesArray];
    [mailComposer setSubject:self.dicEmailClient[@"emailSubject"]];
    
    NSArray* arrayName = [dicProjectInfo[@"contact_name"] componentsSeparatedByString: @" "];
    NSString *strFirstName = arrayName[0];
    
    NSString *strBody = [NSString stringWithFormat:@"%@,\n\n %@ http://ec2-54-165-80-46.compute-1.amazonaws.com/home/#/client/%@ %@ %@\n%@\n%@", strFirstName, self.dicEmailClient[@"emailBeforeLink"], dicProjectInfo[@"project_ID"], self.dicEmailClient[@"emailAfterLink"], self.dicEmailClient[@"nameSalesPerson"] ,self.dicEmailClient[@"emailSalesPerson"], self.dicEmailClient[@"phoneSalesPerson"]];
    [mailComposer setMessageBody:strBody isHTML:NO];
    //Opens the view for sending an email
    [self presentViewController:mailComposer animated:YES completion:nil];
}

//After users sends the email or cancels, the view will return to the SingleEventView
- (void)mailComposeController:(MFMailComposeViewController *)controller didFinishWithResult:(MFMailComposeResult)result error:(NSError *)error
{
    [self dismissViewControllerAnimated:YES completion:nil];
    [self disablesAutomaticKeyboardDismissal];
}

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender
{
    if([segue.identifier isEqualToString:@"NewProjectToMapNew"])
    {
        Map *vc = segue.destinationViewController;
        vc.isNewProject = YES;
    }
    else
    {
        CGPoint buttonPosition = [sender convertPoint:CGPointZero toView:self.tableView];
        NSIndexPath *indexPath = [self.tableView indexPathForRowAtPoint:buttonPosition];
        
        NSDictionary *dicProjectInfo;
        if([self.strAssemblyType isEqualToString:@"ACTIVE"])
        {
            dicProjectInfo = [self.activeProjects objectAtIndex:indexPath.row];
        }
        else if([self.strAssemblyType isEqualToString:@"INACTIVE"])
        {
            dicProjectInfo = [self.inactiveProjects objectAtIndex:indexPath.row];
        }
        else if([self.strAssemblyType isEqualToString:@"CLOSED"])
        {
            dicProjectInfo = [self.closedProjects objectAtIndex:indexPath.row];
        }
        
        if([segue.identifier isEqualToString:@"ExistingProjectToMapNew"])
        {
            Map *vc = segue.destinationViewController;
            vc.isNewProject = NO;
            vc.strProjectID = [NSString stringWithFormat:@"%f", [[dicProjectInfo objectForKey:@"project_ID"] doubleValue]];
            vc.strNavBarTitle = [dicProjectInfo objectForKey:@"project_name"];
            vc.strCostPerKWH = [NSString stringWithFormat:@"%d", [[dicProjectInfo objectForKey:@"power_cost_per_kWh"]intValue]];
            vc.strDateOfService = [NSString stringWithFormat:@"%d", [[dicProjectInfo objectForKey:@"date_of_service"]intValue]];
            if([vc.strDateOfService isEqualToString:@"0"])
            {
                vc.strDateOfService = @"";
            }
            vc.strNameOfRep = [dicProjectInfo objectForKey:@"contact_name"];
            vc.strPhone = [dicProjectInfo objectForKey:@"contact_phone"];
            vc.strEmail = [dicProjectInfo objectForKey:@"contact_email"];
            vc.strComments = [dicProjectInfo objectForKey:@"comments"];
        }
        else if([segue.identifier isEqualToString:@"ProjectsToPresentation"])
        {
            Presentation *vc = segue.destinationViewController;
            vc.strProjectID = [NSString stringWithFormat:@"%f", [[dicProjectInfo objectForKey:@"project_ID"] doubleValue]];;
            vc.strProjectTitle = [dicProjectInfo objectForKey:@"project_name"];
        }
    }
}
@end
