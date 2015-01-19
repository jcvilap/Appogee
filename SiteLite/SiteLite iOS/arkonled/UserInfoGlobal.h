//
//  UserInfoGlobal.h
//  Pi Delta Psi
//
//  Created by Michael Nation on 3/15/14.
//  Copyright (c) 2014 Mike Nation Industries. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface UserInfoGlobal : NSObject

- (void)setUserID:(NSString *)UserID;
- (NSString *)getUserID;

- (void)setUserType:(NSString *)UserType;
- (NSString *)getUserType;
@end
