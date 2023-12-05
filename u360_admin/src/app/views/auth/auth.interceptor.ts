import { Injectable } from "@angular/core";
import {
    HttpRequest,
    HttpHandler,
    HttpInterceptor,
    HttpErrorResponse,
    HttpClient
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
        private http: HttpClient,
        private authService: AuthService,
        private router: Router,
        private spinner: NgxSpinnerService,
        public generalService: GeneralService,
        private toastr: ToastrService,
    ) { }
    refresh = false;

    intercept(request: HttpRequest<any>, next: HttpHandler) {
        this.toastr.clear();
        const accessToken = this.authService.getToken();
        this.generalService.showSpinner();
        if (accessToken) {
            const req = request.clone({
                setHeaders: {
                    authorization: `Bearer ${accessToken}`
                }
            });
            return next.handle(req).pipe(catchError((err: HttpErrorResponse) => {
                this.spinner.hide();
                if ((err.status === 401 || err.status === 403) && !this.refresh) {
                    this.refresh = true;
                    localStorage.removeItem('token');
                    localStorage.removeItem('userId');
                    localStorage.removeItem('name');
                    localStorage.removeItem('image');
                    localStorage.removeItem('device_type');
                    localStorage.removeItem('device_token');
                    this.router.navigate(['/login']);
                }
                this.refresh = false;
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