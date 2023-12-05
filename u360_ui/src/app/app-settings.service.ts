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
  apiUrl = environment.API_URL;
  // Login
  public login(user: User): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/admin/login', user);
  }
  public getDashboardCardDetails(): Observable<any> {
    return this.http.get<any>(this.apiUrl + '/user/dashboard-cards');
  }
  public getDashboardUserChartDetails(postData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/user/user-details-chart',postData);
  }
  public getMapChartDetails(postData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/user/country-map',postData);
  }
  public getDashboardSponserChartDetails(postData:any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/user/sponser-chart-details',postData);
  }
  public getDashboardFooterDetails(): Observable<any> {
    return this.http.get<any>(this.apiUrl + '/user/dashboard-info');
  }
  public refreshToken(token: any) {
    return this.http.post(this.apiUrl + '/admin/refreshtoken', token);
  }
  public forgotPassword(email: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/forgot-password', email);
  }
  public resetPassword(password: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/reset-password', password);
  }
  public changePassword(password: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/password-change', password);
  }
  public getUserDetails(): Observable<any> {
    return this.http.get<any>(this.apiUrl + '/get-user-details');
  }
  public getProfileDetails(): Observable<any> {
    return this.http.get<any>(this.apiUrl + '/profile');
  }
  public updateProfile(formData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/profile-change', formData);
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

  //Front Login
  public frontLogin(user: User): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/login', user);
  }
  public getGoogleBirthDate(accessToken:any){
    return this.http.get<any>(`https://people.googleapis.com/v1/people/me?personFields=birthdays&access_token=${accessToken}`);
  }
  public registerUser(formData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/register-user', formData);
  }
  public verifyEmail(formData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/register-user', formData);
  }
  public saveUserDetails(formData: any, stepNo: number, stepType: string): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/save-user-details/' + stepType + '/' + stepNo, formData);
  }
  public getCountriesList(): Observable<any> {
    return this.http.get<any>(this.apiUrl + '/project/get-countries');
  }
  public getSdgsList(): Observable<any> {
    return this.http.get<any>(this.apiUrl + '/project/get-sdgs');
  }
  public getUserInterestList(): Observable<any> {
    return this.http.get<any>(this.apiUrl + '/project/get-user-interest');
  }
  public getCategoriesList(): Observable<any> {
    return this.http.get<any>(this.apiUrl + '/project/get-categories');
  }
  public saveProjectDetails(formData: any, stepNo: number, stepType: string): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/user/project/save/' + stepType + '/' + stepNo, formData);
  }
  public getFrontUserProjectDetails(id:any): Observable<any> {
    let projectDetails=this.http.get<any>(this.apiUrl + '/user/project/details/'+id);
    let imageList =this.http.post<any>(this.apiUrl + '/user/project/get-docs',{'id':id,'type':'image'});
    let videoList =this.http.post<any>(this.apiUrl + '/user/project/get-docs',{'id':id,'type':'video'});
    let docList =this.http.post<any>(this.apiUrl + '/user/project/get-docs',{'id':id,'type':'document'});
    return forkJoin([projectDetails,imageList,videoList,docList]);
  }
  public getProjectCommonList(): Observable<any> {
    let categoryList =this.http.get<any>(this.apiUrl + '/project/get-categories');
    let countryList =this.http.get<any>(this.apiUrl + '/project/get-countries');
    let sdgList =this.http.get<any>(this.apiUrl + '/project/get-sdgs');
    return forkJoin([categoryList, countryList, sdgList]);
  }
  public getIndustryList(): Observable<any> {
    return this.http.get<any>(this.apiUrl + '/project/get-industries');    
  }
  public getProjectDetails(id:any): Observable<any> {
    return this.http.get<any>(this.apiUrl + '/project/details/'+id);
  }
  public uploadProjectDoc(formData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/user/project/save-docs', formData);
  }
  public saveProjectReview(formData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/user/project/save-review', formData);
  }
  public addVolunteer(formData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/user/project/add-volunteer', formData);
  }
  public addDonation(formData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/user/project/add-donation', formData);
  }
  public projectDocumentDelete(id:any): Observable<any> {
    return this.http.delete<any>(this.apiUrl + '/user/project/delete-docs/'+id);
  }
  public removeCoverImage(id:any): Observable<any> {
    return this.http.delete<any>(this.apiUrl + '/user/project/delete-cover-image/'+id);
  }
  public getProjectDocumentList(id:any,type:string): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/user/project/get-docs',{'id':id,'type':type});
  }
  public getUerProjectsList(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/user/project/list', postData);
  }
  public deleteProject(id: any): Observable<any> {
    return this.http.delete<any>(this.apiUrl + '/user/project/delete/' + id);
  }
  public followUnfollowProject(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/user/project/follow/create', postData);
  }
  public getAllProjectsList(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/project/list', postData);
  }
  public getAboutUsPage(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/get-cms-page', postData);
  }
  public getSiteSetting(): Observable<any> {
    return this.http.get<any>(this.apiUrl + '/get-site-settings');
  }
  public getUserProjectDetails(id: any): Observable<any> {
    return this.http.get<any>(this.apiUrl + '/user/project/details/' + id);
  }
  public getFeelGoodPortalList(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/user/project/get-feel-good-data', postData);
  }
  public esgReportsEmail(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/save-esg-report-mails', postData);
  }
  public saveSubscribeEmail(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/save-subsciption-mails', postData);
  }
  public closeProject(id: any): Observable<any> {
    return this.http.get<any>(this.apiUrl + '/user/project/close/' + id);
  }
  public getUserFollowedProjectsList(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/user/project/followed-project-list', postData);
  }
  public getUserCompletedProjectsList(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/user/project/completed-project-list', postData);
  }
  public updateUserDetails(formData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/update-user-details', formData);
  }
  public getAllProjectReviews(formData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/project/reviews', formData);
  }
  public getAllDonation(formData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/user/donation/list', formData);
  }
  public stopDonationRecurring(postData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/user/project/stop-recurring-donation', postData);
  }
  public updateProfileImage(formData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/profile-image-change', formData);
  }

  //community api: starts
  public saveCommunity(formData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/user/project/community/add', formData);
  }
  public likeUnlike(formData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/user/project/community/like', formData);
  }
  public getCommunityDetails(formData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/project/community/list', formData);
  }
  public addComment(formData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/user/project/community/comment/add', formData);
  }
  public getUserProjectsCommunityDetails(formData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/project/community', formData);
  }
  //community api: ends
}
