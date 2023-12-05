import { AppSettingsService } from './../../app-settings.service';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ApiOperationManagerService } from 'src/app/services/api/operation-manager/api/api-operation-manager.service';
import { ToastrService } from 'ngx-toastr';
import { NgxSpinnerService } from 'ngx-spinner';
import {
  AbstractControl,
  UntypedFormBuilder,
  UntypedFormGroup,
  Validators,
} from '@angular/forms';
import { NgbTypeahead } from '@ng-bootstrap/ng-bootstrap';
import { GeneralService } from 'src/app/services/general.service';
@Component({
  selector: 'app-edit-user',
  templateUrl: './edit-user.component.html',
  styleUrls: ['./edit-user.component.scss']
})
export class EditUserComponent implements OnInit {
  user_id!: number;
  profileEdit: UntypedFormGroup = new UntypedFormGroup({});
  submitted = false;
  isSuperUser: boolean = false;
  login: any;
  imageURL: any;
  imageFile: { link: string;} | undefined;

  constructor(
    private route: ActivatedRoute,
    public router: Router,
    private spinner: NgxSpinnerService,
    private toastr: ToastrService,
    private formBuilder: UntypedFormBuilder,
    private appSettingsService: AppSettingsService,
    private generalService : GeneralService,
  ) {
    this.login = localStorage.getItem('name') || '';
  }

  ngOnInit(): void {
    this.generateFormObj();
    this.getProfileDetail();
  }

  generateFormObj() {
    this.profileEdit = this.formBuilder.group({
      name: ["", Validators.required],
      email: ["", Validators.required],
      image: [""]
    });
  }

  imagesPreview(event: { target: { files: Blob[]; }; srcElement: { files: { name: any; }[]; }; }) {
    if (event.target.files && event.target.files[0]) {
      const file = event.target.files[0];
      this.profileEdit.patchValue({
        image: file
      });
        const reader = new FileReader();
        
        reader.onload = (_event: any) => {
            this.imageFile = {
                link: _event.target.result,
                //file: event.srcElement.files[0],
                //name: event.srcElement.files[0].name
            };
        };
        reader.readAsDataURL(event.target.files[0]);
    }
  }

  getProfileDetail() {
    this.appSettingsService.getProfileDetails().subscribe((res) => {
      if(res.success== true){
        let profile = res.data.userDetails;
        if (profile) {
          this.imageFile = {
              link: profile.profile_image,
              //file: './assets/images/',
              //name: profile.image
          };
          this.profileEdit = this.formBuilder.group({
            name: [profile.name, Validators.required],
            email: [profile.email, Validators.required],
            image: [profile.profile_image],
          });
        }
        this.toastr.success(res.message, "Success");
      }else{
        this.toastr.success(res.message, "error");
      }
      
    },
    (error) => {     
      this.spinner.hide();        
      this.generalService.getErrorMsg(error.error.message);
    });
  }

  get f(): { [key: string]: AbstractControl } {
    return this.profileEdit.controls;
  }

  onSubmit() {
    this.submitted = true;

    if (this.profileEdit.invalid) {
      return;
    }
    
    const formData = new FormData();
    Object.keys(this.profileEdit.value).forEach(key => {
      formData.append(key, this.profileEdit.value[key]);
    })
    this.spinner.show();
    
    this.appSettingsService.updateProfile(formData).subscribe((res:any) => {
      if (res) {
        this.spinner.hide();
        if(res.success==true){
          var admin = res.data.profileDetails;
          localStorage.setItem("name", admin.name);
          localStorage.setItem("image", admin.profile_image);
          this.generalService.passData(admin);
          this.toastr.success(res.message, "Success");
          //this.router.navigate(['/dashboard']);
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
