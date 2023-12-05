import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ApiOperationManagerService } from 'src/app/services/api/operation-manager/api/api-operation-manager.service';
import { ToastrService } from 'ngx-toastr';
import { NgxSpinnerService } from 'ngx-spinner';
import { Constants } from 'src/app/services/constants';
import { passwordChangeFilter } from 'src/app/data/filter/common/password-change';
import { AuthService } from 'src/app/views/auth/auth.service';
import { AppSettingsService } from './../../app-settings.service';
declare const $: any;
import {
  AbstractControl,
  UntypedFormBuilder,
  UntypedFormGroup,
  Validators,
} from '@angular/forms';
import { GeneralService } from 'src/app/services/general.service';
@Component({
  selector: 'app-security-settings',
  templateUrl: './change-password.component.html',
  styleUrls: ['./change-password.component.css'],
})
export class ChangePasswordComponent implements OnInit {
  user_id!: number;
  ChangePasswordForm: UntypedFormGroup = new UntypedFormGroup({});
  submitted = false;
  disabled = false
  constructor(
    public router: Router,
    private activeAouter: ActivatedRoute,
    private apiService: ApiOperationManagerService,
    private spinner: NgxSpinnerService,
    private toastr: ToastrService,
    private formBuilder: UntypedFormBuilder,
    private authSevice: AuthService,
    private appSettingsService: AppSettingsService,
    private generalService: GeneralService
  ) { }

  ngOnInit(): void {
    this.generateFormObj();
  }

  generateFormObj() {
    this.ChangePasswordForm = this.formBuilder.group(
      {
        current_password: ['', Validators.required],
        new_password: [
          '',
          [
            Validators.required,
            Validators.pattern(
              '^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[$@$!%*?&])[A-Za-zd$@$!%*?&].{8,}$'
            ),
          ],
        ],
        confirm_password: ['', Validators.required],
      },
      {
        validators: [this.mustMatch('new_password', 'confirm_password')],
      }
    );
  }

  get f(): { [key: string]: AbstractControl } {
    return this.ChangePasswordForm.controls;
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

  onSubmit() {
    this.submitted = true;
    this.disabled = true;

    if (this.ChangePasswordForm.invalid) {
      return;
    }
    this.spinner.show();
    this.appSettingsService.chnagePassword(this.ChangePasswordForm.value).subscribe((res: any) => {
      if (res) {
        this.spinner.hide();
        if (res.success == true) {
          this.toastr.success(res.message, "Success");
          this.authSevice.logout();
          // this.router.navigate(['login']);
          // this.router.navigate(['/dashboard']);
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
    if (this.showPassword == true) {
      this.showPassword=false;
    } else {
      this.showPassword=true;
    }
  }
  showNewPassword: boolean = false;
  toggleShowNewPassword() {
    if (this.showNewPassword == true) {
      this.showNewPassword=false;
    } else {
      this.showNewPassword=true;
    }
  }
  showConfirmPassword: boolean = false;
  toggleShowConfirmPassword() {
    if (this.showConfirmPassword == true) {
      this.showConfirmPassword=false;
    } else {
      this.showConfirmPassword=true;
    }
  }

  makeSaveButtonActive(){
    this.disabled = false;
}
}
