//
//  CreateNewAccount.m
//  SiteLite
//
//  Created by Michael Nation on 10/29/14.
//  Copyright (c) 2014 ArkonLED. All rights reserved.
//

#import "CreateNewAccount.h"

@interface CreateNewAccount ()

@end

@implementation CreateNewAccount

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    //Display Nav Bar
    [self.navigationController setNavigationBarHidden:NO];
    
    self.txtFirstName.text = @"";
    self.txtLastName.text = @"";
    self.txtEmail.text = @"";
    self.txtPassword.text = @"";
    self.txtPasswordConfirm.text = @"";
    
    self.tableView.rowHeight = 44;
    
    //Hide seperator lines for empty cells
    self.tableView.tableFooterView = [[UIView alloc] initWithFrame:CGRectZero];
    
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (IBAction)submitBtnClicked:(id)sender
{
    if([self.txtFirstName.text isEqualToString:@""])
    {
        UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"WARNING" message:@"Enter First Name." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
        
        [errorView show];
        return;
    }
    
    if([self.txtLastName.text isEqualToString:@""])
    {
        UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"WARNING" message:@"Enter Last Name." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
        
        [errorView show];
        return;
    }
    
    if([self.txtEmail.text isEqualToString:@""])
    {
        UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"WARNING" message:@"Enter Email." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
        
        [errorView show];
        return;
    }
    
    if([self.txtPassword.text isEqualToString:@""])
    {
        UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"WARNING" message:@"Enter Password." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
        
        [errorView show];
        return;
    }
    
    if([self.txtPasswordConfirm.text isEqualToString:@""])
    {
        UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"WARNING" message:@"Confirm Password." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
        
        [errorView show];
        return;
    }
    
    if(![self.txtPassword.text isEqualToString:self.txtPasswordConfirm.text])
    {
        UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"WARNING" message:@"Password and Confirm Password do not match. Please enter again." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
        
        [errorView show];
        
        self.txtPassword.text = @"";
        self.txtPasswordConfirm.text = @"";
        return;
    }
    
    
    //Insert new row in database
    NSString *myRequestString = [NSString stringWithFormat:@"firstName=%@&lastName=%@&email=%@&password=%@", self.txtFirstName.text, self.txtLastName.text, self.txtEmail.text, [self calculateSHA:self.txtPassword.text]];
    
    // Create Data from request
    NSData *myRequestData = [NSData dataWithBytes:[myRequestString UTF8String] length:[myRequestString length]];
    NSMutableURLRequest *request = [[NSMutableURLRequest alloc] initWithURL:[NSURL URLWithString: @"http://ec2-54-165-80-46.compute-1.amazonaws.com/iOS/AccountInfo/createNewUser.php"]];
    // set Request Type
    [request setHTTPMethod: @"POST"];
    // Set content-type
    [request setValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"content-type"];
    // Set Request Body
    [request setHTTPBody: myRequestData];
    // Now send a request and get Response
    [NSURLConnection sendAsynchronousRequest:request queue:[NSOperationQueue mainQueue] completionHandler:^(NSURLResponse * response, NSData * data,NSError * error)
     {
         if(!error)
         {
             NSDictionary *dicServerMessage = [NSJSONSerialization JSONObjectWithData:data options:NSJSONReadingMutableContainers error:nil];
             
             //New Account Created Successfully
             if([[dicServerMessage objectForKey:@"success"] isEqualToNumber:@1])
             {
                 UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"COMPLETE" message:[dicServerMessage objectForKey:@"message"] delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
                 
                 [errorView show];
                 
                 //Hide nav bar
                 [self.navigationController setNavigationBarHidden:YES animated:NO];
                 
                 [self.navigationController popViewControllerAnimated:YES];
             }
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
}

- (NSString *)calculateSHA:(NSString *)yourString
{
    const char *ptr = [yourString UTF8String];
    
    int i =0;
    int len = (int)strlen(ptr);
    Byte byteArray[len];
    while (i!=len)
    {
        unsigned eachChar = *(ptr + i);
        unsigned low8Bits = eachChar & 0xFF;
        
        byteArray[i] = low8Bits;
        i++;
    }
    
    
    unsigned char digest[CC_SHA1_DIGEST_LENGTH];
    
    CC_SHA1(byteArray, len, digest);
    
    NSMutableString *hex = [NSMutableString string];
    for (int i=0; i<20; i++)
        [hex appendFormat:@"%02x", digest[i]];
    
    NSString *immutableHex = [NSString stringWithString:hex];
    
    return immutableHex;
}

- (IBAction)cancelBtnClicked:(id)sender
{
    //Hide Nav Bar
    [self.navigationController setNavigationBarHidden:YES];
    
    [self.navigationController popViewControllerAnimated:YES];
}

- (IBAction)hideKeyboard:(id)sender
{
    [self.view endEditing:YES];
}



//Tableview Delegate Methods**********************************************************
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
//Tableview Delegate Methods DONE**********************************************************

@end
