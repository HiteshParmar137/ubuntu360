import { Component, OnInit } from '@angular/core';
import {
  AbstractControl,
  UntypedFormBuilder,
  UntypedFormGroup,
  Validators,
} from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ApiOperationManagerService } from 'src/app/services/api/operation-manager/api/api-operation-manager.service';
import { ToastrService } from 'ngx-toastr';
import { NgxSpinnerService } from 'ngx-spinner';
import { Constants } from 'src/app/services/constants';
import { resetPasswordFilter } from 'src/app/data/filter/reset-password/reset-password-filter';
import { verifyEmailFilter } from 'src/app/data/filter/reset-password/verify-email-filter';
import { AppSettingsService } from "src/app/app-settings.service";
import { GeneralService } from 'src/app/services/general.service';

@Component({
  selector: 'app-reset-password',
  templateUrl: './reset-password.component.html',
  styleUrls: ['./reset-password.component.css'],
})
export class ResetPasswordComponent implements OnInit {
  resetToken !: string;
  resetPasswordForm: UntypedFormGroup = new UntypedFormGroup({});
  submitted = false;
  token: string = '';
  constructor(
    private formBuilder: UntypedFormBuilder,
    private route: ActivatedRoute,
    public router: Router,
    private apiService: ApiOperationManagerService,
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private appSettingsService: AppSettingsService,
    private generalService: GeneralService
  ) { }

  ngOnInit(): void {
    // if (this.route.snapshot.paramMap.get('token')) {
    //   this.token = this.route.snapshot.paramMap.get('token') || '';
    // }
    this.resetToken = this.route.snapshot.params['resetToken'];
    //this.spinner.show();
    this.generateFormObj();
  }

  get f(): { [key: string]: AbstractControl } {
    return this.resetPasswordForm.controls;
  }

  generateFormObj() {
    this.resetPasswordForm = this.formBuilder.group(
      {
        password: [
          '',
          [
            Validators.required,
            Validators.pattern(
              '^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[$@$!%*?&])[A-Za-zd$@$!%*?&].{8,}$'
            ),
          ],
        ],
        confirm_password: ['', Validators.required],
        token: [''],
      },
      {
        validators: [this.mustMatch('password', 'confirm_password')],
      }
    );
  }

  mustMatch(controlName: string, matchingControlName: string) {
    return (formGroup: UntypedFormGroup) => {
      const control = formGroup.controls[controlName];
      const matchingControl = formGroup.controls[matchingControlName];
      if (matchingControl.errors && !matchingControl.errors['matching']) {
        return;
      }
      if (control.value !== matchingControl.value) {
        matchingControl.setErrors({ matching: true });
      } else {
        matchingControl.setErrors(null);
      }
    };
  }

  onSubmit(): void {

    this.submitted = true;
    if (this.resetPasswordForm.invalid) {
      return;
    }
    this.spinner.show();
    this.appSettingsService.resetPassword(this.resetPasswordForm.value).subscribe((res: any) => {
      if (res) {
        this.spinner.hide();
        if (res.success === true) {
          this.toastr.success(res.message, "Success");
          this.router.navigate(['login']);
        } else {
          this.generalService.getErrorMsg(res.message);
        }
      }
    },
    (error) => {     
      this.spinner.hide();        
      this.generalService.getErrorMsg(error.error.message);
    });
  }
  showPassword: boolean = false;
  toggleShowPassword() {
    if (this.showPassword === true) {
      this.showPassword=false;
    } else {
      this.showPassword=true;
    }
  }
  showConfirmPassword: boolean = false;
  toggleShowConfirmPassword() {
    if (this.showConfirmPassword === true) {
      this.showConfirmPassword=false;
    } else {
      this.showConfirmPassword=true;
    }
  }
}
