import { Component, OnInit } from '@angular/core';
import { verifyEmailFilter } from 'src/app/data/filter/reset-password/verify-email-filter';
import { ApiOperationManagerService } from 'src/app/services/api/operation-manager/api/api-operation-manager.service';
import { ActivatedRoute, Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { GeneralService } from 'src/app/services/general.service';

@Component({
  selector: 'app-verify-email',
  templateUrl: './verify-email.component.html',
  styleUrls: ['./verify-email.component.css'],
})
export class VerifyEmailComponent implements OnInit {
  token: string = '';

  constructor(
    private apiService: ApiOperationManagerService,
    private route: ActivatedRoute,
    private router: Router,
    private spinner: NgxSpinnerService,
    private toastr: ToastrService,
    private generalService: GeneralService
  ) {}

  ngOnInit(): void {
    // get token from query params
    if (this.route.snapshot.paramMap.get('token')) {
      this.token = this.route.snapshot.paramMap.get('token') || '';
    }
    this.spinner.show();
    // call verification function
    this.verifyEmail();
  }

  verifyEmail() {
    // generate request object
    const tokenObj: verifyEmailFilter = new verifyEmailFilter(
      this.token,
      'verify_email'
    );
    // call verify api
    this.apiService
      .verifyEmail(tokenObj)
      .then((response) => {
        if (response.data.success === true) {
          this.spinner.hide();
          this.toastr.success(response.data.message, 'Success');
          // /this.router.navigate(['']);
        } else {
          this.generalService.getErrorMsg(response.data.message);
          this.spinner.hide();
        }
      })
      .catch((err: any) => {
        this.spinner.hide();
        this.router.navigate(['']);
      });
  }
}
