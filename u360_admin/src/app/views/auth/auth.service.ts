import { HttpClient } from "@angular/common/http";
import { Injectable } from '@angular/core';
import { Router } from "@angular/router";
import { ToastrService } from "ngx-toastr";
import { Observable } from "rxjs";
import { environment } from "src/environments/environment";
import { NgxSpinnerService } from 'ngx-spinner';
export class User {
  email!: String;
  password!: String;
  password_confirmation!: String;
}
@Injectable({
  providedIn: 'root',
})
export class AuthService {
  currentUserValue: any;
  constructor(
    private http: HttpClient,
    private toastr: ToastrService,
    public router: Router,
    private spinner: NgxSpinnerService,
  ) {
  }
  apiUrl = environment.API_URL;
  // Login
  login(user: User): Observable<any> {
    return this.http.post<any>(this.apiUrl + '/admin/login', user);
  }

  isAuthorized(roleCode: any) {
    throw new Error('Method not implemented.');
  }

  isLoggedIn() {
    if (localStorage.getItem('token') && localStorage.getItem('token') !== '') {
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
  public logOut(): Observable<any> {
    return this.http.get<any>(this.apiUrl + '/admin/logout');
  }
  logout() {
    console.log('1');
    this.spinner.show();
    this.logOut().subscribe((res: any) => {
      if (res) {
        console.log('2');
        this.spinner.hide();
        if (res.success == true) {
          localStorage.removeItem('token');
          localStorage.removeItem('userId');
          localStorage.removeItem('name');
          localStorage.removeItem('image');
          this.toastr.success(res.message, "Success");
          this.router.navigate(['/login']);
        } else {
          this.toastr.error(res.message, "error");
          this.router.navigate(['/login']);
        }
      }
    }, (error: any) => {
      this.toastr.error(error.error.message, "error");
      this.router.navigate(['/login']);
  });
  }
}
