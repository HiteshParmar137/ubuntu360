import { Injectable } from '@angular/core';
import { CoreService } from '../../core/core.service';
import * as CoreModels from '../../core/models/response-data/response-data';
import { ApiOperationManagerService } from '../api/api-operation-manager.service';
import { Constants } from 'src/app/services/constants';
import { loginFilter } from 'src/app/data/filter/login/login-filter';
import { statesFilter} from 'src/app/data/filter/common/states-filter';
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

function ResponseDataFromJSON(target: any, propertyKey: any, descriptor: any) {
  let originalMethod = descriptor.value;
  descriptor.value = function (...args: any[]) {
    let result = new Promise((resolve, reject) => {
      return Promise.all([originalMethod.apply(this, args)])
        .then((responses) => {
          let response: CoreModels.ResponseData<any> = responses[0];
          if (response.data) {
            response.data = response.data;
          }
          resolve(response);
        })
        .catch((err) => {
          reject(err);
        });
    });
    return result;
  };
}

@Injectable({
  providedIn: 'root',
})
export class OperationManagerService extends ApiOperationManagerService {
  constructor(private apiService: CoreService) {
    super();
  }

  @ResponseDataFromJSON
  login(loginFilter: loginFilter): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doPost(Constants.LOGIN, JSON.stringify(loginFilter));
  }
  getSidebar(): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doGet(
      Constants.SIDEBAR,
      JSON.stringify(statesFilter)
    );
  }
  getSystemModule(): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doGet(
      Constants.GETSYSTEMMODULES,
      JSON.stringify(statesFilter)
    );
  }
  getUserList(userListFilter:userListFilter): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doPost(
      Constants.USERLIST,
      JSON.stringify(userListFilter)
    );
  }
  
  userCreate(
    userCreateFilter: userCreateFilter
  ): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doPost(
      Constants.USERCREATE,
      JSON.stringify(userCreateFilter)
    );
  }

  userEdit(
    userEditFilter: userEditFilter
  ): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doPost(
      Constants.USEREDIT,
      JSON.stringify(userEditFilter)
    );
  }

  getUserDetail(id:number): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doGet(
      Constants.USERDETAIL+id,
      JSON.stringify(statesFilter)
    );
  }
  
  userGroupDetail(id:string): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doGet(
      Constants.USERGROUPDETAIL + id,
      JSON.stringify(statesFilter)
    );
  }

  getUserGroupList(groupListFilter:groupListFilter): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doPost(
      Constants.USERGROUP,
      JSON.stringify(groupListFilter)
    );
  }
  
  userGroupCreate(groupAddFilter: groupAddFilter): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doPost(Constants.USERGROUPCREATE, JSON.stringify(groupAddFilter));
  }

  userGroupUpdate(groupEditFilter: groupEditFilter): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doPost(Constants.USERGROUPUPDATE, JSON.stringify(groupEditFilter));
  }

  deleteUserGroup(id:string): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doPost(
      Constants.USERGROUPDELETE + id,
      JSON.stringify(statesFilter)
    );
  }

  deleteUser(id:string): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doPost(
      Constants.USERDELETE + id,
      JSON.stringify(statesFilter)
    );
  }
  
  getDashboardCount(): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doGet(
      Constants.DASHBOARDCOUNT,
      JSON.stringify(statesFilter)
    );
  }

  getStrandsList(strandsListFilter:strandsListFilter): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doPost(
      Constants.STRANDSLIST,
      JSON.stringify(strandsListFilter)
    );
  }

  strandsCreate(strandsCreateFilter: strandsCreateFilter): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doPost(Constants.STRANDSCREATE, JSON.stringify(strandsCreateFilter));
  }

  strandsDelete(id:string): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doPost(
      Constants.STRANDSDELETE + id,
      JSON.stringify(statesFilter)
    );
  }

  strandsDetail(id:string): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doGet(
      Constants.STRANDSDETAIL + id,
      JSON.stringify(statesFilter)
    );
  }

  strandsUpdate(strandsEditFilter: strandsEditFilter): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doPost(Constants.STRANDSUPDATE, JSON.stringify(strandsEditFilter));
  }
  
  getAclChecker(module_name:string,action:string): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doGet(
      Constants.ACLCHECKER+module_name+'&action='+action,
      JSON.stringify(statesFilter)
    );
  }

  passwordChange(passwordChangeFilter: passwordChangeFilter): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doPost(Constants.PASSWORDCHANGE, JSON.stringify(passwordChangeFilter));
  }
  
  userGroupForDropdown(): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doGet(
      Constants.USERGROUPFORDROPDOWN,
      JSON.stringify(statesFilter)
    );
  }

  forgetPassword(
    forgetPasswordFilter: forgetPasswordFilter
  ): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doPost(
      Constants.FORGOT_PASSWORD,
      JSON.stringify(forgetPasswordFilter)
    );
  }
  resetPassword(
    resetPasswordFilter: resetPasswordFilter
  ): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doPost(
      Constants.RESET_PASSWORD,
      JSON.stringify(resetPasswordFilter)
    );
  }

  verifyEmail(
    verifyEmailFilter: verifyEmailFilter
  ): Promise<CoreModels.ResponseData<any>> {
    return this.apiService.doPost(
      Constants.VERIFY_EMAIL,
      JSON.stringify(verifyEmailFilter)
    );
  }

}
