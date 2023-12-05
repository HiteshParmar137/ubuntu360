import { Injectable } from "@angular/core";
import {
    HttpRequest,
    HttpHandler,
    HttpInterceptor,
    HttpErrorResponse,
} from "@angular/common/http";
import { AuthService } from "./auth.service";
import { Router } from "@angular/router"
import { catchError, throwError } from 'rxjs';
import { NgxSpinnerService } from 'ngx-spinner';
import { AppSettingsService } from 'src/app/app-settings.service';
import { ToastrService } from 'ngx-toastr';
import { GeneralService } from 'src/app/services/general.service';
import { finalize } from 'rxjs/operators';
@Injectable()
export class AuthInterceptor implements HttpInterceptor {
    constructor(
        private toastr: ToastrService,
        private authService: AuthService,
        private router: Router,
        private spinner: NgxSpinnerService,
        private appSettingsService: AppSettingsService,
        public generalService: GeneralService,
    ) { }
    refresh = false;
    intercept(request: HttpRequest<any>, next: HttpHandler) {
        let spinnerRun=true;
        
        const url = request.url;
        let searchTerm = 'get-site-settings'; 
        
        if (url.indexOf(searchTerm) !== -1 ) {
            spinnerRun=false;
        }
        searchTerm = 'project/get'; // Word to search in the API URL\
        if (spinnerRun && url.indexOf(searchTerm) !== -1 ) {
            spinnerRun=false;
        }
        
        if(spinnerRun){
            this.generalService.showSpinner();
        }
        const accessToken = this.authService.getToken();
        if (accessToken) {
            const req = request.clone({
                setHeaders: {
                    authorization: `Bearer ${accessToken}`
                }
            });
            return next.handle(req).pipe(catchError((err: HttpErrorResponse) => {
                if ((err.status === 401 || err.status === 403 || err.status === 404) && !this.refresh) {
                    this.refresh = true;
                    this.appSettingsService.refreshToken({ token: accessToken }).subscribe({
                        next : (res: any) => {
                            if (res?.status_code==200) {
                                const newAccessToken = res.token;
                                localStorage.setItem("token", newAccessToken);
                                this.authService.storeAccessToken(newAccessToken)
                                return next.handle(request.clone({
                                    setHeaders: {
                                        Authorization: `Bearer ${newAccessToken}`
                                    }
                                }));
                            }else{
                                localStorage.removeItem('token');
                                localStorage.removeItem('userId');
                                localStorage.removeItem('name');
                                localStorage.removeItem('image');
                                localStorage.removeItem('device_type');
                                localStorage.removeItem('device_token');
                                return this.router.navigate(['/login']);
                            }
                        },
                        error : (error) => {
                            localStorage.removeItem('token');
                            localStorage.removeItem('userId');
                            localStorage.removeItem('name');
                            localStorage.removeItem('image');
                            localStorage.removeItem('device_type');
                            localStorage.removeItem('device_token');
                            return this.router.navigate(['/login']);
                        }
                    });
                }
                return throwError(() => err);
            }),finalize(() => {
                this.generalService.hideSpinner();
            }));
        }
        return next.handle(request).pipe(
            finalize(() => {
                this.generalService.hideSpinner();
            })
        );
    }
}