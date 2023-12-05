import { Injectable } from '@angular/core';
import * as Models from '../models/response-data/response-data';
import { environment } from 'src/environments/environment';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { CoreService } from '../core.service';
import { AuthService } from 'src/app/views/front-user/auth/auth.service';
import { Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { NgxSpinnerService } from 'ngx-spinner';
declare const $: any;

@Injectable({
  providedIn: 'root'
})
export class RequestService extends CoreService {

  basrUrl = environment.API_URL;
  
  constructor(private http: HttpClient,
    private authSevice:AuthService,
    public router: Router,
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    ) { 
    super();
  }

  async init(): Promise<void> {
    return;
  }

  public doGet(method: string, data: string): Promise<Models.ResponseData<any>> {
    let headers = new HttpHeaders();
    headers = headers.set('Access-Control-Allow-Origin', '*');
    headers = headers.set('Content-Type', 'application/json');
    headers = headers.set('Access-Control-Allow-Credentials', 'true');

    if (localStorage.getItem('token') !== undefined && localStorage.getItem('token') !== null) {
      headers = headers.set('Authorization', 'bearer' + localStorage.getItem('token'));
    }
    if(localStorage.getItem('module_name') && localStorage.getItem('action')){
      if(localStorage.getItem('module_name') !== '' && localStorage.getItem('action') !== ''){
        let module_name: any = localStorage.getItem('module_name');
        let action: any = localStorage.getItem('action');
        headers = headers.set('action-module',module_name);
        headers = headers.set('action-type', action);
      }
    }
    var url = this.basrUrl+method;
    return new Promise((resolve, reject) => {
      this.http.get(url,{headers: headers})
      .toPromise()
      .then(res => { // Success
          let response: Models.ResponseData<any> = {};
          response.data = res;
          return (resolve(response));
        }
      ).catch((err) => {
        if(err.error.status_code === 401){
          this.authSevice.logout();
        }
        if(err.error.status_code === 403){
          this.toastr.error(err.error.message, 'Error');
          this.spinner.hide();
          this.router.navigate(['dashboard']);
        }else{
          reject(err);
        }
      });
    })
  }

  public doPost(method: string, data: string): Promise<Models.ResponseData<any>> {
    let headers = new HttpHeaders();
    headers = headers.set('Access-Control-Allow-Origin', '*');
    headers = headers.set('Content-Type', 'application/json');
    headers = headers.set('Access-Control-Allow-Credentials', 'true');

    if (localStorage.getItem('token') !== undefined && localStorage.getItem('token') !== null) {
      headers = headers.set('Authorization', 'bearer ' + localStorage.getItem('token'));
    }
    if(localStorage.getItem('module_name') && localStorage.getItem('action')){
      if(localStorage.getItem('module_name') !== '' && localStorage.getItem('action') !== ''){
        let module_name: any = localStorage.getItem('module_name');
        let action: any = localStorage.getItem('action');
        headers = headers.set('action-module',module_name);
        headers = headers.set('action-type', action);
      }
    }
    var url = this.basrUrl+method;
    return new Promise((resolve, reject) => {
      this.http.post(url, data, {headers: headers})
      .toPromise()
      .then(res => { // Success
          let response: Models.ResponseData<any> = {};
          response.data = res;
          return (resolve(response));
        }
      ).catch((err) => {
        if(err.error.status_code === 401){
          this.authSevice.logout();
        }
        if(err.error.status_code === 403){
          this.toastr.error(err.error.message, 'Error');
          this.spinner.hide();
          this.router.navigate(['dashboard']);
        }else{
          reject(err);
        }
      });
    })
  } 
}
