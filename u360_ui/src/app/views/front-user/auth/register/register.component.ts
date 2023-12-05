import { Component, OnInit } from '@angular/core';
import { AppSettingsService } from 'src/app/app-settings.service';
import { Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { NgxSpinnerService } from 'ngx-spinner';
import { UntypedFormBuilder, UntypedFormGroup, Validators } from "@angular/forms";
import { GeneralService } from 'src/app/services/general.service';
import { SocialAuthService,GoogleLoginProvider,FacebookLoginProvider,SocialUser } from "@abacritt/angularx-social-login";

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.scss']
})
export class RegisterComponent implements OnInit {
  registerForm: UntypedFormGroup = new UntypedFormGroup({});
  isSubmitted = false;
  deviceToken: any = '';
  deviceType: number = 0;
  socialId: any = 0;
  googlePassUser: any = {};
  credentialsObj: any = {};
  socialUser!: SocialUser;
  isLoggedin?: boolean = undefined;
  constructor(
    private formBuilder: UntypedFormBuilder,
    private apiServvice: AppSettingsService,
    private toast: ToastrService,
    private spinner: NgxSpinnerService,
    public router: Router,
    private generalService: GeneralService,
    private socialAuthService: SocialAuthService,
  ) { 
    
  }

  ngOnInit(): void {
    this.generateFormObj();
    this.socialAuthService.authState.subscribe({
      next:(user) => {
        this.socialUser = user;
        this.googlePassUser = {
          'email': user.email,
          'name': user.name,
          'social_id': user.id,
          'password': '',
          'device_type':0
        };
        this.loginCommon(this.googlePassUser);
        this.isLoggedin = user != null;
      },
      error:(error) => {             
        this.generalService.getErrorMsg(error.error.message);
      }
    });
  }
  signInWithGoogle(): void {
    this.socialAuthService.signIn(GoogleLoginProvider.PROVIDER_ID);
  }
  signInWithFB(): void {
    this.socialAuthService.signIn(FacebookLoginProvider.PROVIDER_ID);
  }
  loginCommon(credentialsObj: any) {
    this.apiServvice.frontLogin(credentialsObj).subscribe({
      next:(res: any) => {
        if (res) {
          if (res.success && res.data != null) {
            const user = res.data;
            localStorage.setItem("token", user.token);
            localStorage.setItem("userId", user.id);
            localStorage.setItem("name", user.name);
            localStorage.setItem("image", user.userImage);
            localStorage.setItem("is_signup_completed", user.is_signup_completed);
            if (user.device_token && user.device_token != '') {
              localStorage.setItem("device_token", user.device_token);
            }
            localStorage.setItem("device_type", user.device_type);
            this.toast.success('User registration successfuly.', "Success");
            if(user.is_signup_completed==1){
              this.router.navigate(["dashboard"]);
            }else{
              this.router.navigate(["signup-step"]);
            }
          } else {
            this.generalService.getErrorMsg(res.message);
          }
        }
      },
      error:(error) => {        
        this.generalService.getErrorMsg(error.error.message);
      }
    });
  }
  generateFormObj() {
    this.registerForm = this.formBuilder.group({
      name: ["", Validators.required],
      email: ["", [Validators.required, Validators.email]],
      password: ['',
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

  get f() {
    return this.registerForm.controls;
  }

  onSubmit() {
    this.isSubmitted = true;
    if (this.registerForm.invalid) {
      return;
    }
    this.registerForm.value.device_token = this.deviceToken;
    this.registerForm.value.device_type = this.deviceType;
    this.registerForm.value.social_id = this.socialId;
    this.apiServvice.registerUser(this.registerForm.value).subscribe({
      next:(res: any) => {
        if (res) {
          if (res.success) {
            this.router.navigate(["login"]);
            this.toast.success(res.message, "success");
          } else {
            this.generalService.getErrorMsg(res.message);
          }
        }
      },
      error:(error) => {             
        this.generalService.getErrorMsg(error.error.message);
      }
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
  showConfirmPassword: boolean = false;
  toggleShowConfirmPassword() {
    if (this.showConfirmPassword) {
      this.showConfirmPassword=false;
    } else {
      this.showConfirmPassword=true;
    }
  }
}
