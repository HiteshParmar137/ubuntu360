import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ApiOperationManagerService } from 'src/app/services/api/operation-manager/api/api-operation-manager.service';
import { ToastrService } from 'ngx-toastr';
import { NgxSpinnerService } from 'ngx-spinner';
import { AuthService } from 'src/app/views/front-user/auth/auth.service';
import { AppSettingsService } from './../../../app-settings.service';
declare const $: any;
import {
  AbstractControl,
  UntypedFormBuilder,
  UntypedFormGroup,
  Validators,
} from '@angular/forms';
import { GeneralService } from 'src/app/services/general.service';
@Component({
  selector: 'user-change-password',
  templateUrl: './change-password.component.html',
  styleUrls: ['./change-password.component.css'],
})
export class ChangePasswordComponent implements OnInit {
  user_id!: number;
  ChangePasswordForm: UntypedFormGroup = new UntypedFormGroup({});
  submitted = false;
  disabled = false;
  showPassword: boolean = false;
  showNewConfirmPassword: boolean = false;
  showNewPassword: boolean = false;
  constructor(
    public router: Router,
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
    this.appSettingsService.changePassword(this.ChangePasswordForm.value).subscribe({
      next:(res: any) => {
        if (res) {
          this.spinner.hide();
          if (res.success) {
            this.toastr.success(res.message, "Success");
            this.authSevice.logout()
          } else {
            this.generalService.getErrorMsg(res.message);
          }
        }
      },
      error:  (error) => {
        this.generalService.getErrorMsg(error.error.message);
      }
    });
  }

  makeSaveButtonActive() {
    this.disabled = false;
  }
  toggleShowPassword() {
    if (this.showPassword) {
      this.showPassword = false;
    } else {
      this.showPassword = true;
    }
  }
  toggleNewShowPassword() {
    if (this.showNewPassword) {
      this.showNewPassword = false;
    } else {
      this.showNewPassword = true;
    }
  }
  toggleNewConfirmShowPassword() {
    if (this.showNewConfirmPassword) {
      this.showNewConfirmPassword = false;
    } else {
      this.showNewConfirmPassword = true;
    }
  }
}
