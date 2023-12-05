import { Component, OnInit } from "@angular/core";
import {
  AbstractControl,
  UntypedFormBuilder,
  UntypedFormGroup,
  Validators,
} from "@angular/forms";
import { Router } from "@angular/router";
import { ApiOperationManagerService } from "src/app/services/api/operation-manager/api/api-operation-manager.service";
import { ToastrService } from "ngx-toastr";
import { NgxSpinnerService } from "ngx-spinner";
import { AppSettingsService } from "src/app/app-settings.service";
import { GeneralService } from "src/app/services/general.service";

@Component({
  selector: "app-login",
  templateUrl: "./login.component.html",
  styleUrls: ["./login.component.css"],
})
export class LoginComponent implements OnInit {
  loginForm: UntypedFormGroup = new UntypedFormGroup({});
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
    this.generateLoginFormObj();
  }

  get f(): { [key: string]: AbstractControl } {
    return this.loginForm.controls;
  }

  generateLoginFormObj() {
    this.loginForm = this.formBuilder.group({
      email: ["", [Validators.required, Validators.email]],
      password: ["", [Validators.required]],
    });
  }

  loginFormSubmit() {
    this.submitted = true;
    if (this.loginForm.invalid) {
      return;
    }
    this.spinner.show();
    
    this.appSettingsService.login(this.loginForm.value).subscribe((res:any) => {       
      if (res) {
        this.spinner.hide();
        if(res.success==true){
          var user=res.data;
          localStorage.setItem("token", user.token);
          localStorage.setItem("userId", user.id);
          localStorage.setItem("name", user.name);
          localStorage.setItem("image", user.adminImage);
          localStorage.setItem("admin_module", JSON.stringify(user.admin_module));
          this.router.navigate(["dashboard"]);
          this.toastr.success(res.message, "Success");
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

  showPassword: boolean = false;
  toggleShowPassword() {
    if (this.showPassword) {
      this.showPassword=false;
    } else {
      this.showPassword=true;
    }
  }
  showNewPassword: boolean = false;
  toggleShowNewPassword() {
    if (this.showNewPassword) {
      this.showNewPassword=false;
    } else {
      this.showNewPassword=true;
    }
  }
  showConfirmPassword: boolean = false;
  toggleShowConfirmPassword() {
    if (this.showConfirmPassword) {
      this.showConfirmPassword=false;
    } else {
      this.showConfirmPassword=true;
    }
  }
}
