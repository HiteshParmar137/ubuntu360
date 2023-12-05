import { HttpClient } from "@angular/common/http";
import { Injectable } from '@angular/core';
import { Router } from "@angular/router";
import { ToastrService } from "ngx-toastr";
import { Observable } from "rxjs";
import { environment } from "src/environments/environment";
//import { GeneralService } from 'src/app/services/general.service';
import {
  SocialAuthService,
  GoogleLoginProvider,
  SocialUser,
  FacebookLoginProvider,
} from '@abacritt/angularx-social-login';
export class User {
  name!: String;
  email!: String;
  password!: String;
  password_confirmation!: String;
}
@Injectable({
  providedIn: 'root',
})
export class AuthService {
  currentUserValue: any;
  logOutDataObj: any = {};
  device_token: any = '';
  device_type: number = 0;
  constructor(
    private http: HttpClient,
    private toastr: ToastrService,
    public router: Router,
    //public generalService: GeneralService,
    private socialAuthService: SocialAuthService
  ) {

  }
  apiUrl = environment.API_URL;
  // Login
  // login(user: User): Observable<any> {
  //   return this.http.post<any>('http://localhost/u360_api/public/api/admin/login', user);
  // }

  isAuthorized(roleCode: any) {
    throw new Error('Method not implemented.');
  }

  isLoggedIn() {
    if (localStorage.getItem('token') && localStorage.getItem('token') !== '' && localStorage.getItem('is_signup_completed') && localStorage.getItem('is_signup_completed') == '1') {
      return true;
    } else {
      return false;
    }
  }
  storeAccessToken = (token: string) => {
    localStorage.setItem('token', token)
  }
  getToken() {
    return localStorage.getItem('token');
  }
  getRefreshToken = () => {
    return localStorage.getItem('refreshToken')
  }
  getDeviceToken = () => {
    return localStorage.getItem('device_token')
  }
  getDeviceType = () => {
    return localStorage.getItem('device_type')
  }
  public logOut(formData: any): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/logout', formData);
  }
  logout() {
    if (this.getDeviceToken() && this.getDeviceToken() != null) {
      this.device_token = this.getDeviceToken();
    }
    this.logOutDataObj.device_token = this.device_token;
    this.logOutDataObj.device_type = this.getDeviceType();
    this.socialAuthService.signOut();
    this.logOut(this.logOutDataObj).subscribe((res: any) => {
      if (res) {
        localStorage.removeItem('token');
        localStorage.removeItem('userId');
        localStorage.removeItem('name');
        localStorage.removeItem('image');
        localStorage.removeItem('device_type');
        this.router.navigate(['/login']);
        if (this.getDeviceToken() && this.getDeviceToken() != null) {
          localStorage.removeItem('device_token');
        }
        if (res.success == true) {
          this.toastr.success(res.message, "Success");
        } else {
          this.toastr.error(res.message, "error");
        }
      }
    }, (error: any) => {
      localStorage.removeItem('token');
      localStorage.removeItem('userId');
      localStorage.removeItem('name');
      localStorage.removeItem('image');
      localStorage.removeItem('device_type');
      localStorage.removeItem('device_token');
      return this.router.navigate(['/login']);
    });
  }
}
