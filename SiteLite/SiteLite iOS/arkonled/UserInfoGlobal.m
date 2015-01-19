//
//  UserInfoGlobal.m
//  Pi Delta Psi
//
//  Created by Michael Nation on 3/15/14.
//  Copyright (c) 2014 Mike Nation Industries. All rights reserved.
//

#import "UserInfoGlobal.h"

static NSString *userID;
static NSString *userType;

@implementation UserInfoGlobal

- (void)setUserID:(NSString *)UserID
{
    userID = UserID;
}

- (NSString *)getUserID
{
    return userID;
}

- (void)setUserType:(NSString *)UserType
{
    userType = UserType;
}

- (NSString *)getUserType
{
    return userType;
}
@end
