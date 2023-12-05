export class Constants {
  static readonly API_ERROR = 'Something went wrong';

  // API List for Employer
  static readonly LOGIN = 'login';
  static readonly PROJECTFOLDER='projects';
  static readonly USERGROUP = 'user_groups/list';
  static readonly SIDEBAR = 'get_sidebar';
  static readonly GETSYSTEMMODULES = 'get_system_modules';
  static readonly USERLIST = 'admin_users/list';
  static readonly USERCREATE = 'admin_users/create';
  static readonly USEREDIT = 'admin_users/edit';
  static readonly USERDETAIL = 'admin_users/';
  static readonly USERDELETE = 'admin_users/delete/';
  static readonly USERGROUPDETAIL = 'user_groups/';
  static readonly USERGROUPCREATE = 'user_groups/create';
  static readonly USERGROUPUPDATE = 'user_groups/edit';
  static readonly USERGROUPDELETE = 'user_groups/delete/';
  static readonly STRANDSLIST = 'strands/list';
  static readonly STRANDSCREATE = 'strands/create';
  static readonly STRANDSUPDATE = 'strands/edit';
  static readonly STRANDSDELETE = 'strands/delete/';
  static readonly STRANDSDETAIL = 'strands/';
  static readonly DASHBOARDCOUNT = 'getStats';
  static readonly ACLCHECKER = 'check_permissions?module_name=';
  static readonly PASSWORDCHANGE = 'admin_password_change';
  static readonly USERGROUPFORDROPDOWN = 'user_group_list';
  static readonly FORGOT_PASSWORD = 'admin_forgot_password';
  static readonly RESET_PASSWORD = 'admin_reset_password';
  static readonly VERIFY_EMAIL = '/verify-email';
  static readonly VERIFIED = 'Verified';
  static readonly FRONTUSERFOLDER = 'front-user';
  static readonly PROJECTSTATUSARR = [
    { id: "Draft", name: "Draft" },
    { id: "Pending", name: "Pending" },
    { id: "Completed", name: "Completed" }
  ];
}
