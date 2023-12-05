import { Component, OnInit } from '@angular/core';
import {
  AbstractControl,
  UntypedFormBuilder,
  UntypedFormGroup,
  Validators,
} from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
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
  isSubmitted = false;
  constructor(
    private formBuilder: UntypedFormBuilder,
    private route: ActivatedRoute,
    public router: Router,
    private toastr: ToastrService,
    private appSettingsService: AppSettingsService,
    private generalService: GeneralService
  ) { }

  ngOnInit(): void {
    this.resetToken = this.route.snapshot.params['resetToken'];
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
    this.appSettingsService.resetPassword(this.resetPasswordForm.value).subscribe({
      next: (res: any) => {
        if (res) {
          if (res.success) {
            this.toastr.success(res.message, "Success");
            this.router.navigate(['login']);
          } else {
            this.generalService.getErrorMsg(res.message);
          }
        }
      },
      error: (error) => {
        this.generalService.getErrorMsg(error.error.message);
      }
    });
  }
  showPassword: boolean = false;
  toggleShowPassword() {
    if (this.showPassword) {
      this.showPassword = false;
    } else {
      this.showPassword = true;
    }
  }
  showConfirmPassword: boolean = false;
  toggleShowConfirmPassword() {
    if (this.showConfirmPassword) {
      this.showConfirmPassword = false;
    } else {
      this.showConfirmPassword = true;
    }
  }
}
