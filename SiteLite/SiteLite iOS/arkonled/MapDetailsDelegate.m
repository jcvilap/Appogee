//
//  MapDetailsDelegate.m
//  ArkonLED
//
//  Created by Michael Nation on 9/21/14.
//  Copyright (c) 2014 ArkonLED. All rights reserved.
//

#import "MapDetailsDelegate.h"

@interface MapDetailsDelegate ()

@end

@implementation MapDetailsDelegate

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    self.tableView.rowHeight = 44;
    
    //Load textfields
    self.txtProjectName.text = self.strProjectName;
    self.txtCost.text = self.strCost;
    self.txtNameOfRep.text = self.strNameOfRep;
    self.txtPhone.text = self.strPhone;
    self.txtEmail.text = self.strEmail;
    self.txtComments.text = self.strComments;
    self.txtDateOfService.text = self.strDateOfService;
    
    //Hide seperator lines for empty cells
    self.tableView.tableFooterView = [[UIView alloc] initWithFrame:CGRectZero];
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (IBAction)doneBtnClicked:(id)sender
{
    if([self.txtProjectName.text isEqualToString:@""])
    {
        UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"WARNING" message:@"Enter Project Name." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
        
        [errorView show];
        return;
    }
    
    if([self.txtCost.text isEqualToString:@""])
    {
        UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"WARNING" message:@"Enter Cost per kWh." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
        
        [errorView show];
        return;
    }
    
    if([self.txtDateOfService.text isEqualToString:@""])
    {
        UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"WARNING" message:@"Enter Date of Service." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
        
        [errorView show];
        return;
    }
    
    if([self.txtCost.text intValue] < 1 || [self.txtCost.text intValue] > 100)
    {
        UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"WARNING" message:@"Cost per kWh is invalid. Enter a value between 1 and 100." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
        
        [errorView show];
        return;
    }
    
    if([self.txtDateOfService.text intValue] < 1900 || [self.txtDateOfService.text intValue] > 2050)
    {
        UIAlertView *errorView = [[UIAlertView alloc] initWithTitle:@"WARNING" message:@"Date of Service format is incorrect or out of range. Enter 4 digits - YYYY" delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
        
        [errorView show];
        return;
    }
    
    NSMutableDictionary *dicProjectDetails = [[NSMutableDictionary alloc] init];
    dicProjectDetails[@"projectName"] = self.txtProjectName.text;
    dicProjectDetails[@"cost"] = self.txtCost.text;
    dicProjectDetails[@"dateOfService"] = self.txtDateOfService.text;
    dicProjectDetails[@"contactName"] = self.txtNameOfRep.text;
    dicProjectDetails[@"contactPhone"] = self.txtPhone.text;
    dicProjectDetails[@"contactEmail"] = self.txtEmail.text;
    dicProjectDetails[@"comments"] = self.txtComments.text;
    
    //Return to Map
    [self.delegate doneMapDetails:dicProjectDetails andStatus:self.isNewProject];
}

- (IBAction)cancelBtnClicked:(id)sender
{
    if(self.isNewProject)
    {
        [self.navigationController popToViewController:[self.navigationController.viewControllers objectAtIndex:1] animated:YES];
    }
    else
    {
        [self.navigationController popViewControllerAnimated:YES];
    }
}

- (IBAction)dismissKeyboard:(id)sender
{
    [self.view endEditing:YES];
}

- (BOOL)textField:(UITextField *)textField shouldChangeCharactersInRange:(NSRange)range replacementString:(NSString *)string
{
    NSString *newString = [textField.text stringByReplacingCharactersInRange:range withString:string];
    NSArray *components = [newString componentsSeparatedByCharactersInSet:[[NSCharacterSet decimalDigitCharacterSet] invertedSet]];
    NSString *decimalString = [components componentsJoinedByString:@""];
    
    NSUInteger length = decimalString.length;
    BOOL hasLeadingOne = length > 0 && [decimalString characterAtIndex:0] == '1';
    
    if (length == 0 || (length > 10 && !hasLeadingOne) || (length > 11)) {
        textField.text = decimalString;
        return NO;
    }
    
    NSUInteger index = 0;
    NSMutableString *formattedString = [NSMutableString string];
    
    if (hasLeadingOne) {
        [formattedString appendString:@"1 "];
        index += 1;
    }
    
    if (length - index > 3) {
        NSString *areaCode = [decimalString substringWithRange:NSMakeRange(index, 3)];
        [formattedString appendFormat:@"(%@) ",areaCode];
        index += 3;
    }
    
    if (length - index > 3) {
        NSString *prefix = [decimalString substringWithRange:NSMakeRange(index, 3)];
        [formattedString appendFormat:@"%@-",prefix];
        index += 3;
    }
    
    NSString *remainder = [decimalString substringFromIndex:index];
    [formattedString appendString:remainder];
    
    textField.text = formattedString;
    
    return NO;
}

-(NSString*)formatNumber:(NSString*)mobileNumber
{
    mobileNumber = [mobileNumber stringByReplacingOccurrencesOfString:@"(" withString:@""];
    mobileNumber = [mobileNumber stringByReplacingOccurrencesOfString:@")" withString:@""];
    mobileNumber = [mobileNumber stringByReplacingOccurrencesOfString:@" " withString:@""];
    mobileNumber = [mobileNumber stringByReplacingOccurrencesOfString:@"-" withString:@""];
    mobileNumber = [mobileNumber stringByReplacingOccurrencesOfString:@"+" withString:@""];
    
    
    int length = (int)[mobileNumber length];
    if(length > 10)
    {
        mobileNumber = [mobileNumber substringFromIndex: length-10];
        
    }
    
    
    return mobileNumber;
}


-(int)getLength:(NSString*)mobileNumber
{
    mobileNumber = [mobileNumber stringByReplacingOccurrencesOfString:@"(" withString:@""];
    mobileNumber = [mobileNumber stringByReplacingOccurrencesOfString:@")" withString:@""];
    mobileNumber = [mobileNumber stringByReplacingOccurrencesOfString:@" " withString:@""];
    mobileNumber = [mobileNumber stringByReplacingOccurrencesOfString:@"-" withString:@""];
    mobileNumber = [mobileNumber stringByReplacingOccurrencesOfString:@"+" withString:@""];
    
    int length = (int)[mobileNumber length];
    
    return length;
    
    
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
