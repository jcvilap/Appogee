//
//  Login.m
//  ArkonLED
//
//  Created by Michael Nation on 9/7/14.
//  Copyright (c) 2014 ArkonLED. All rights reserved.
//

#import "Login.h"

@interface Login ()

@property (strong, nonatomic) UserInfoGlobal *userInfoGlobal;

@end

@implementation Login

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
    
    //Hide Nav bar
    [self.navigationController setNavigationBarHidden:YES animated:NO];
    
    //Ajust Cell Height
    self.tableView.rowHeight = [[UIScreen mainScreen] bounds].size.height/3;
    
    [self.tableView setBackgroundView:[[UIImageView alloc] initWithImage:[UIImage imageNamed:@"loginBackgroundBlack.png"]]];
    //Change Content Mode for image button
    self.tableView.backgroundView.contentMode = UIViewContentModeScaleAspectFit;
    [self.tableView.backgroundView setClipsToBounds:YES];
    
    self.userInfoGlobal = [[UserInfoGlobal alloc] init];
    
    self.txtEmail.text = @"";
    self.txtPassword.text = @"";
    
    if([SSKeychain passwordForService:@"arkonled" account:@"email"] != nil)
    {
        self.txtEmail.text = [SSKeychain passwordForService:@"arkonled" account:@"email"];
        self.txtPassword.text = [SSKeychain passwordForService:@"arkonled" account:@"password"];
        [self.btnRememberMe setTitle:@"X" forState:UIControlStateNormal];
        
        [self loginBtnClicked:self];
    }
    
}

- (void)viewDidAppear:(BOOL)animated
{
    if([SSKeychain passwordForService:@"arkonled" account:@"email"] == nil)
    {
        self.txtEmail.text = @"";
        self.txtPassword.text = @"";
        [self.btnRememberMe setTitle:@"" forState:UIControlStateNormal];
    }
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (IBAction)loginBtnClicked:(id)sender
{
    if([self.txtEmail.text isEqualToString:@""])
    {
        UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"WARNING" message:@"Enter your Email." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
        
        [errorView show];
        return;
    }
    
    if([self.txtPassword.text isEqualToString:@""])
    {
        UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"WARNING" message:@"Enter your Password." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
        
        [errorView show];
        return;
    }
    
    NSString *myRequestString = [NSString stringWithFormat:@"email=%@&password=%@", self.txtEmail.text, [self calculateSHA:self.txtPassword.text]];
    
    // Create Data from request
    NSData *myRequestData = [NSData dataWithBytes:[myRequestString UTF8String] length:[myRequestString length]];
    NSMutableURLRequest *request = [[NSMutableURLRequest alloc] initWithURL:[NSURL URLWithString: @"http://ec2-54-165-80-46.compute-1.amazonaws.com/iOS/AccountInfo/login.php"]];
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
             
             //Login Successful
             if([[dicServerMessage objectForKey:@"success"] isEqualToNumber:@1])
             {
                 //Remember Me is Checked
                 if([self.btnRememberMe.currentTitle isEqualToString:@"X"])
                 {
                     //If no keychain exist or if username/password has changed, update keychain
                     if([SSKeychain passwordForService:@"arkonled" account:@"email"] == nil || ![[SSKeychain passwordForService:@"arkonled" account:@"email"] isEqualToString:self.txtEmail.text] || ![[SSKeychain passwordForService:@"arkonled" account:@"password"] isEqualToString:self.txtPassword.text])
                     {
                         [SSKeychain setPassword:self.txtEmail.text forService:@"arkonled" account:@"email"];
                         [SSKeychain setPassword:self.txtPassword.text forService:@"arkonled" account:@"password"];
                     }
                     
                 }
                 //Unchecked
                 else
                 {
                     self.txtEmail.text = @"";
                     self.txtPassword.text = @"";
                 }
                 
                 [self.userInfoGlobal setUserID:[NSString stringWithFormat:@"%ld", [[dicServerMessage objectForKey:@"userID"] longValue]]];
                 [self.userInfoGlobal setUserType:[NSString stringWithFormat:@"%ld", [[dicServerMessage objectForKey:@"userType"] longValue]]];
                 
                 
                 [self performSegueWithIdentifier:@"LoginToProjectsList" sender:self];
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

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath;
{
    return [[UIScreen mainScreen] bounds].size.height/3;
}

- (void)tableView:(UITableView *)tableView willDisplayCell:(UITableViewCell *)cell forRowAtIndexPath:(NSIndexPath *)indexPath
{
    [cell setBackgroundColor:[UIColor clearColor]];
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

- (IBAction)dismissKeyboard:(id)sender
{
    [self.view endEditing:YES];
}

- (IBAction)btnRememberMeClicked:(id)sender
{
    if([self.btnRememberMe.currentTitle isEqualToString:@"X"])
    {
        [self.btnRememberMe setTitle:@"" forState:UIControlStateNormal];
    }
    else
    {
        [self.btnRememberMe setTitle:@"X" forState:UIControlStateNormal];
    }
}


@end
