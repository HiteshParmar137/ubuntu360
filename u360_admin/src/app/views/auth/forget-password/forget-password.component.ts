import { Component, OnInit } from '@angular/core';
import {
  AbstractControl,
  UntypedFormBuilder,
  UntypedFormGroup,
  Validators,
} from '@angular/forms';
import { Router } from '@angular/router';
import { ApiOperationManagerService } from 'src/app/services/api/operation-manager/api/api-operation-manager.service';
import { ToastrService } from 'ngx-toastr';
import { NgxSpinnerService } from 'ngx-spinner';
import { Constants } from 'src/app/services/constants';
import { forgetPasswordFilter } from 'src/app/data/filter/forget-password/forget-password-filter';
import { AppSettingsService } from "src/app/app-settings.service";
import { GeneralService } from 'src/app/services/general.service';

@Component({
  selector: 'app-forget-password',
  templateUrl: './forget-password.component.html',
  styleUrls: ['./forget-password.component.css'],
})
export class ForgetPasswordComponent implements OnInit {
  fogetPasswordForm: UntypedFormGroup = new UntypedFormGroup({});
  submitted = false;

  constructor(
    private formBuilder: UntypedFormBuilder,
    public router: Router,
    private apiService: ApiOperationManagerService,
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private appSettingsService: AppSettingsService,
    private generalService: GeneralService
  ) {}

  ngOnInit(): void {
    this.generateFormObj();
  }

  get f(): { [key: string]: AbstractControl } {
    return this.fogetPasswordForm.controls;
  }

  generateFormObj() {
    this.fogetPasswordForm = this.formBuilder.group({
      email: ['', [Validators.required, Validators.email]],
    });
  }

  onSubmit(): void {
    this.submitted = true;
    if (this.fogetPasswordForm.invalid) {
      return;
    }
    this.spinner.show();
    this.appSettingsService.forgotPassword(this.fogetPasswordForm.value).subscribe((res:any) => {       
      if (res) {
        this.spinner.hide();
        if(res.status_code==200){
          this.toastr.success(res.message, "Success");
          //this.showUserDashboard(1);
        }else{
          this.generalService.getErrorMsg(res.message);
        }
      }
      this.spinner.hide();
    },
    (error) => {     
      this.spinner.hide();        
      this.generalService.getErrorMsg(error.error.message);
    });
  }
}
