import { HttpClient } from "@angular/common/http";
import { Injectable } from "@angular/core";
import { Observable,forkJoin} from "rxjs";
import { environment } from './../environments/environment';
// User interface
export class User {
  name!: String;
  email!: String;
  password!: String;
  password_confirmation!: String;
}
@Injectable({
  providedIn: "root",
})
export class AppSettingsService {
  constructor(private http: HttpClient) {

  }
  apiUrl=environment.API_URL;
  // Login
  public login(user:User): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/login', user);
  }
  public refreshToken(token:any) {
    return this.http.post(this.apiUrl + '/refresh-token', token);
  }
  public forgotPassword(email:any): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/forgot-password', email);
  }
  public resetPassword(password:any): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/reset-password', password);
  }
  public chnagePassword(password:any): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/password-change', password);
  }
  public getProfileDetails(): Observable<any> {
    return this.http.get<any>(this.apiUrl+'/admin/profile');
  }
  public updateProfile(formData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/profile-change', formData);
  }
  public changeProfile(user:any): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/admin_reset_password', user);
  }
  public getAdminUser(postData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/admin_users/list',postData);
  }
  public getAdminUserDetail(id:any): Observable<any> {
    return this.http.get<any>(this.apiUrl+'/admin/admin_users/'+id);
  }
  public adminUserAdd(formData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/admin_users/create', formData);
  }
  public adminUserUpdate(formData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/admin_users/update', formData);
  }
  public deleteAdminUser(id:any): Observable<any> {
    return this.http.delete<any>(this.apiUrl+'/admin/admin_users/delete/'+id);
  }
  public getUsers(postData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/users/list',postData);
  }
  public getUserDetails(id:any): Observable<any> {
    return this.http.get<any>(this.apiUrl+'/admin/users/get-user/'+id);
  }
  public getEmailTemplateList(postData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/templates-management/list', postData);
  }
  public getTemplateDetails(id:any): Observable<any> {
    return this.http.get<any>(this.apiUrl+'/admin/templates-management/details/'+id);
  }
  public createTemplate(postData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/templates-management/create', postData);
  }
  public updateTemplate(postData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/templates-management/update', postData);
  }
  public deleteEmailTemplate(id:any): Observable<any> {
    return this.http.delete<any>(this.apiUrl+'/admin/templates-management/delete/'+id);
  }
  public getCmsList(postData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/cms-management/list', postData);
  }
  public getCmsDetails(id:any): Observable<any> {
    return this.http.get<any>(this.apiUrl+'/admin/cms-management/details/'+id);
  }
  public createCms(postData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/cms-management/create', postData);
  }
  public updateCms(postData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/cms-management/update', postData);
  }
  public deleteCms(id:any): Observable<any> {
    return this.http.delete<any>(this.apiUrl+'/admin/cms-management/delete/'+id);
  }
  public getSiteSettingsDetails(search:any): Observable<any> {
    return this.http.get<any>(this.apiUrl+'/admin/site-settings/get-site-settings?search='+search);
  }
  public saveSiteSettings(postData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/site-settings/store', postData);
  }
  public getFeedbackList(postData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/feedback/list', postData);
  }
  public deleteFeedback(id:any): Observable<any> {
    return this.http.delete<any>(this.apiUrl+'/admin/feedback/delete/'+id);
  }
  public getAllModules(): Observable<any> {
    return this.http.get<any>(this.apiUrl+'/admin/roles-management/get-all-modules');
  }
  public getUserGroups(): Observable<any> {
    return this.http.get<any>(this.apiUrl+'/admin/get-user-groups');
  }
  public getJSON(): Observable<any> {
    return this.http.get("./assets/files/user.json");
  }
  public getUserGroupJSON(): Observable<any> {
    return this.http.get("./assets/files/userGroup.json");
  }
  public getModulesJSON(): Observable<any> {
    return this.http.get("./assets/files/modules.json");
  }
  public getSidebarJSON(): Observable<any> {
    return this.http.get("./assets/files/sidebar.json");
  }
  public getPupilsJSON(): Observable<any> {
    return this.http.get("./assets/files/pupils.json");
  }
  public getStaffJSON(): Observable<any> {
    return this.http.get("./assets/files/staff.json");
  }
  public getTutorJSON(): Observable<any> {
    return this.http.get("./assets/files/tutor.json");
  }
  public getStrandsJSON(): Observable<any> {
    return this.http.get("./assets/files/strands.json");
  }
  public getSkillJSON(): Observable<any> {
    return this.http.get("./assets/files/skills.json");
  }
  public getDiplomaJSON(): Observable<any> {
    return this.http.get("./assets/files/diploma.json");
  }
  public getPupilsEvidenceJSON(): Observable<any> {
    return this.http.get("./assets/files/pupilsWithEvidence.json");
  }
  public getEvidenceJSON(): Observable<any> {
    return this.http.get("./assets/files/evidence.json");
  }
  public getsidebarStaffJSON(): Observable<any> {
    return this.http.get("./assets/files/sidebarStaff.json");
  }
  public getsidebarDiplomaJSON(): Observable<any> {
    return this.http.get("./assets/files/sidebarDiplomaManager.json");
  }
  public getsidebarTutorJSON(): Observable<any> {
    return this.http.get("./assets/files/sidebarTutor.json");
  }
  public getsidebarPupilsJSON(): Observable<any> {
    return this.http.get("./assets/files/sidebarPupil.json");
  }
  public getSkillsGraphJSON(): Observable<any> {
    return this.http.get("./assets/files/skillGraph.json");
  }
  public getSidebarDiplomaViewerJSON(): Observable<any> {
    return this.http.get("./assets/files/sidebarDiplomaViewer.json");
  }
  public getProjectsList(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/admin/project/list', postData);
  }
  public approveProject(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/admin/project/approve', postData);
  }
  public rejectProject(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/admin/project/reject', postData);
  }
  public getProjectDonationList(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/admin/project/donation/list', postData);
  }
  public getProjectVolunteerList(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/admin/project/volunteer/list', postData);
  }
  public getProjectFollowersList(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/admin/project/followers/list', postData);
  }
  public getUserProjectsList(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/admin/project/list', postData);
  }
  public deleteProject(id:any): Observable<any> {
    return this.http.delete<any>(this.apiUrl+'/admin/project/delete/'+id);
  }
  public getProjectUsersList(): Observable<any> {
    return this.http.get<any>(this.apiUrl+'/admin/project/get-users');
  }
  public getCategoriesList(): Observable<any> {
    return this.http.get<any>(this.apiUrl + '/project/get-categories');
  }
  
  public getProjectCommonList(): Observable<any> {
    let categoryList =this.http.get<any>(this.apiUrl + '/project/get-categories');
    let countryList =this.http.get<any>(this.apiUrl + '/project/get-countries');
    let sdgList =this.http.get<any>(this.apiUrl + '/project/get-sdgs');
    return forkJoin([categoryList, countryList, sdgList]);
  }
  public getProjectDetails(id:any): Observable<any> {
    let projectDetails=this.http.get<any>(this.apiUrl + '/admin/project/details/'+id);
    let imageList =this.http.post<any>(this.apiUrl + '/admin/project/get-docs',{'id':id,'type':'image'});
    let videoList =this.http.post<any>(this.apiUrl + '/admin/project/get-docs',{'id':id,'type':'video'});
    let docList =this.http.post<any>(this.apiUrl + '/admin/project/get-docs',{'id':id,'type':'document'});
    return forkJoin([projectDetails,imageList,videoList,docList]);
  }
  public saveProjectDetails(formData: any, stepNo: number, stepType: string): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/admin/project/save/' + stepType + '/' + stepNo, formData);
  }
  public uploadProjectDoc(formData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/admin/project/save-docs', formData);
  }
  public projectDocumentDelete(id:any): Observable<any> {
    return this.http.delete<any>(this.apiUrl + '/admin/project/delete-docs/'+id);
  }
  public getTransactionList(postData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/transactions/list', postData);
  }
  public getTransactionUsersList(): Observable<any> {
    return this.http.get<any>(this.apiUrl+'/admin/transactions/get-users');
  }
  public getAdminDashboardData(): Observable<any> {
    return this.http.get<any>(this.apiUrl+'/admin/dashboard');
  }
  public getDashboardCardDetails(): Observable<any> {
    return this.http.get<any>(this.apiUrl + '/admin/dashboard-cards');
  }
  public getDashboardUserChartDetails(postData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/admin/user-details-chart',postData);
  }
  public getMapChartDetails(postData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/admin/country-map',postData);
  }
  public getDashboardSponserChartDetails(postData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/admin/sponser-chart-details',postData);
  }
  public getDashboardFooterDetails(): Observable<any> {
    return this.http.get<any>(this.apiUrl + '/admin/dashboard-info');
  }
  public getFollowedProjectsList(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/admin/project/follow', postData);
  }
  public getEsgReport(postData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/esg-report/list', postData);
  }
  public deleteEsg(id:any): Observable<any> {
    return this.http.delete<any>(this.apiUrl+'/admin/esg-report/delete/'+id);
  }
  public getSubscription(postData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/subscription/list', postData);
  }
  public deleteSubscription(id:any): Observable<any> {
    return this.http.delete<any>(this.apiUrl+'/admin/subscription/delete/'+id);
  }
  public exportEsgReport(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/admin/esg-report/export', postData);
  }
  public exportSubscriptionsReport(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/admin/subscriptions/export', postData);
  }
  public exportSubscription(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/admin/subscriptions/export', postData);
  }
  public getProjectReport(postData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/project/report-list', postData);
  }
  public exportProjectReport(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/admin/project/export-report-list', postData);
  }
  public getUserReport(postData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl+'/admin/users/user-report-list', postData);
  }
  public exportUserReport(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/admin/users/export-user-report-list', postData);
  }
}
