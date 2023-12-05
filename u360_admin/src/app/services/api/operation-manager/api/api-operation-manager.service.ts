import { Injectable } from '@angular/core';
import * as CoreModels from '../../core/models/response-data/response-data';
import { loginFilter } from 'src/app/data/filter/login/login-filter';
import { userCreateFilter } from 'src/app/data/filter/user/user-create-filter';
import { userEditFilter } from 'src/app/data/filter/user/user-edit-filter';
import { groupAddFilter } from 'src/app/data/filter/user-group/group-add-filter';
import { groupEditFilter } from 'src/app/data/filter/user-group/group-edit-filter';
import { groupListFilter } from 'src/app/data/filter/user-group/group-list-filter';
import { passwordChangeFilter } from 'src/app/data/filter/common/password-change';
import { userListFilter } from 'src/app/data/filter/user/user-list-filter';
import { forgetPasswordFilter } from 'src/app/data/filter/forget-password/forget-password-filter';
import { resetPasswordFilter } from 'src/app/data/filter/reset-password/reset-password-filter';
import { verifyEmailFilter } from 'src/app/data/filter/reset-password/verify-email-filter';
import { strandsListFilter } from 'src/app/data/filter/strands/strands-list-filter';
import { strandsCreateFilter } from 'src/app/data/filter/strands/strands-create-filter';
import { strandsEditFilter } from 'src/app/data/filter/strands/strands-edit-filter';

@Injectable({
  providedIn: 'root',
})
export abstract class ApiOperationManagerService {

  abstract login(
    loginFilter: loginFilter
  ): Promise<CoreModels.ResponseData<any>>;

  abstract getSystemModule(): Promise<CoreModels.ResponseData<any>>;

  abstract getSidebar(): Promise<CoreModels.ResponseData<any>>;

  abstract getUserList(userListFilter:userListFilter): Promise<CoreModels.ResponseData<any>>;

  abstract userCreate(
    userCreateFilter: userCreateFilter
  ): Promise<CoreModels.ResponseData<any>>;

  abstract userEdit(
    userEditFilter: userEditFilter
  ): Promise<CoreModels.ResponseData<any>>;

  abstract getUserDetail(id:number): Promise<CoreModels.ResponseData<any>>;
  
  
  abstract getUserGroupList(
    groupListFilter: groupListFilter
    ): Promise<CoreModels.ResponseData<any>>;

  abstract deleteUserGroup(id:string): Promise<CoreModels.ResponseData<any>>;

  abstract deleteUser(id:string): Promise<CoreModels.ResponseData<any>>;
  
  abstract userGroupCreate(
    groupAddFilter: groupAddFilter
  ): Promise<CoreModels.ResponseData<any>>;

  abstract userGroupUpdate(
    groupEditFilter: groupEditFilter
  ): Promise<CoreModels.ResponseData<any>>;

  abstract userGroupDetail(id:string): Promise<CoreModels.ResponseData<any>>;

  abstract getDashboardCount(
    ): Promise<CoreModels.ResponseData<any>>;

  abstract getStrandsList(strandsListFilter:strandsListFilter): Promise<CoreModels.ResponseData<any>>;

  abstract strandsCreate(
    strandsCreateFilter: strandsCreateFilter
  ): Promise<CoreModels.ResponseData<any>>;

  abstract strandsUpdate(
    strandsEditFilter: strandsEditFilter
  ): Promise<CoreModels.ResponseData<any>>;

  abstract strandsDelete(id:string): Promise<CoreModels.ResponseData<any>>;

  abstract strandsDetail(id:string): Promise<CoreModels.ResponseData<any>>;

  abstract getAclChecker(module_name:string,action:string
    ): Promise<CoreModels.ResponseData<any>>;

  abstract passwordChange(
    passwordChangeFilter: passwordChangeFilter
  ): Promise<CoreModels.ResponseData<any>>; 

  abstract userGroupForDropdown(): 
    Promise<CoreModels.ResponseData<any>>;
  
    abstract forgetPassword(
      forgetPasswordFilter: forgetPasswordFilter
    ): Promise<CoreModels.ResponseData<any>>;

    abstract resetPassword(
      resetPasswordFilter: resetPasswordFilter
    ): Promise<CoreModels.ResponseData<any>>;

    abstract verifyEmail(
      verifyEmailFilter: verifyEmailFilter
    ): Promise<CoreModels.ResponseData<any>>;
}
