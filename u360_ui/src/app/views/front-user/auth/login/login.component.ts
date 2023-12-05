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
import { HttpClient } from '@angular/common/http';
// import {
//   SocialAuthService,
//   GoogleLoginProvider,
//   SocialUser,
//   FacebookLoginProvider,
// } from 'angularx-social-login';
import { SocialAuthService,GoogleLoginProvider,FacebookLoginProvider,SocialUser } from "@abacritt/angularx-social-login";
import { GeneralService } from "src/app/services/general.service";

@Component({
  selector: "app-login",
  templateUrl: "./login.component.html",
  styleUrls: ["./login.component.css"],
})
export class LoginComponent implements OnInit {
  loginForm: UntypedFormGroup = new UntypedFormGroup({});
  submitted = false;
  socialUser!: SocialUser;
  isLoggedin?: boolean = undefined;
  googlePassUser: any = {};
  credentialsObj: any = {};
  isDisabled: boolean = false;
  isLoginbuttonDesable:boolean=true;
  constructor(
    private formBuilder: UntypedFormBuilder,
    public router: Router,
    private apiService: ApiOperationManagerService,
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private appSettingsService: AppSettingsService,
    private socialAuthService: SocialAuthService,
    private generalService: GeneralService,
    private http: HttpClient
  ) { }
  ngOnInit(): void {
    this.generateLoginFormObj();
    
    this.socialAuthService.authState.subscribe((user) => {
      this.socialUser = user;
      
      // if (user.provider === 'GOOGLE') {
      //   // For Google
      //   const idToken = user.idToken;
      //   debugger;
      //   // Make a request to the Google People API
      //   const apiKey = 'AIzaSyAGLXUwaZNb0IeK9gvblic3ZqmdNeQ89FY';
      //   this.http.get(`https://people.googleapis.com/v1/people/me?personFields=birthdays&access_token=${idToken}&key=${apiKey}`)
      //     .subscribe((response:any) => {
      //       const birthdate = response['birthdays'][0]['date'];
  
      //     });
      // } else if (user.provider === 'facebook') {
      //   // For Facebook
      //   const accessToken = user.authToken;

      //   // Make a request to the Facebook Graph API
      //   this.http.get(`https://graph.facebook.com/v12.0/me?fields=birthday&access_token=${accessToken}`)
      //     .subscribe((response:any) => {
      //       const birthdate = response['birthday'];
      
      //     });
      // }
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
    (error) => {             
      this.generalService.getErrorMsg(error.error.message);
    });
  }
  signInWithGoogle(): void {
    this.socialAuthService.signIn(GoogleLoginProvider.PROVIDER_ID);
  }
  signInWithFB(): void {
    this.socialAuthService.signIn(FacebookLoginProvider.PROVIDER_ID);
  }

  get f(): { [key: string]: AbstractControl } {
    return this.loginForm.controls;
  }
  generateLoginFormObj() {
    this.loginForm = this.formBuilder.group({
      email: ["", [Validators.required, Validators.email]],
      password: ["", [Validators.required]],
      device_type: ["0", []],
      device_token: ["", []],
    });
  }
  loginFormSubmit() {
    this.submitted = true;
    if (this.loginForm.invalid) {
      return;
    }
    this.loginForm.value.social_id = '';
    this.isDisabled = false;
    this.loginCommon(this.loginForm.value);
  }
  loginCommon(credentialsObj: any) {
    this.appSettingsService.frontLogin(credentialsObj).subscribe({
      next:(res: any) => {
        if (res) {
          if (res.success && res.data != null) {
            const user = res.data;
            localStorage.setItem("token", user.token);
            localStorage.setItem("userId", user.id);
            localStorage.setItem("name", user.name);
            localStorage.setItem("image", user.userImage);
            localStorage.setItem("is_signup_completed", user.is_signup_completed);
            localStorage.setItem("user_type", user.user_type);
            if (user.device_token && user.device_token != '') {
              localStorage.setItem("device_token", user.device_token);
            }
            localStorage.setItem("device_type", user.device_type);
            this.toastr.success(res.message, "Success");
            
            if(user.is_signup_completed==1){
              this.router.navigate(["dashboard"]);
            }else{
              this.router.navigate(["signup-step"]);
            }
          } else {
            this.generalService.getErrorMsg(res.message);
            this.isDisabled = false;
          }
        }
        this.isDisabled = false;
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

  makeEnable(){
    this.isDisabled = false;
  }

}
