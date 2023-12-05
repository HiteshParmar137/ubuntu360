import { AppSettingsService, } from './../../../app-settings.service';
import { Component, OnInit, Renderer2 } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { NgxSpinnerService } from 'ngx-spinner';
import * as $ from 'jquery';
import { AbstractControl, FormArray, FormControl, UntypedFormBuilder, UntypedFormGroup, Validators } from "@angular/forms";
import { GeneralService } from 'src/app/services/general.service';
import { Options } from '@angular-slider/ngx-slider';
import 'bootstrap';
import 'bootstrap-datepicker';
import 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.css';
import 'node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js';
@Component({
  selector: 'app-edit-user',
  templateUrl: './edit-user.component.html',
  styleUrls: ['./edit-user.component.scss']
})
export class EditUserComponent implements OnInit {
  user_id!: number;
  profileEdit: UntypedFormGroup = new UntypedFormGroup({});
  profileImage: UntypedFormGroup = new UntypedFormGroup({});
  aboutEdit: UntypedFormGroup = new UntypedFormGroup({});
  sdgDetails: UntypedFormGroup = new UntypedFormGroup({});
  personalDetails: UntypedFormGroup = new UntypedFormGroup({});
  corporatDetails: UntypedFormGroup = new UntypedFormGroup({});
  socialDetails: UntypedFormGroup = new UntypedFormGroup({});
  interestList:any=[];
  interestDetails: UntypedFormGroup = new UntypedFormGroup({});
  submitted = false;
  isSuperUser: boolean = false;
  login: any;
  imageURL: any;
  imageFile: { link: string; } | undefined;
  userData: any = [];
  age: string = '';
  countries: any = [];
  sdgList: any = [];
  categoryList: any = [];
  sdgOption:any=[];
  interestOption:any=[];
  isCorporateUser:boolean = false;
  isOtherIndustry:boolean = false;
  sponser: string = '';
  options: Options = {
    showTicksValues: true,
    showSelectionBarFromValue:0,
    stepsArray: [
      { value: 1, legend: "Not at all" },
      { value: 2, legend: "Rarely" },
      { value: 3, legend: "Regular" },
    ]
  };
  constructor(
    private route: ActivatedRoute,
    public router: Router,
    private spinner: NgxSpinnerService,
    private toastr: ToastrService,
    private formBuilder: UntypedFormBuilder,
    private appSettingsService: AppSettingsService,
    public generalService: GeneralService,
    private renderer: Renderer2,
  ) {
    this.login = localStorage.getItem('name') ?? '';
  }

  ngOnInit(): void {

    this.generateFormObj();
    this.getProfileDetail();
    this.getUserDetails();
    this.getCountriesList();
    this.generateAboutFormObj();
    this.generateSDGFormObj();
    this.generatePersonalInfoFormObj();
    this.generateCorpoInfoFormObj();
    this.generateSocialFormObj();
    this.getIndustryList();
  
    $('.dob').datepicker({
      format: 'dd/mm/yyyy',
      autoclose: true,
      endDate: '-18y',
    }).on('changeDate', (e) => {
      // Get the selected date
      let selectedDate = e.format('dd/mm/yyyy');

      // Update the form control value with the selected date
      this.personalDetails.controls['dob'].setValue(selectedDate);
    });
  }

  generateFormObj() {
    this.profileEdit = this.formBuilder.group({
      name: ["", Validators.required],
      email: ["", Validators.required],
      image: [""]
    });
  }
  generateAboutFormObj() {
    this.aboutEdit = this.formBuilder.group({
      // formType:[''],
      about: [this.userData.about, [Validators.required,Validators.maxLength(255)]],
    });
  }
  getSdgsList(){
    this.appSettingsService.getSdgsList().subscribe({
      next: (res:any) => {
        this.sdgList = res.data;
        this.generateSDGFormObj();
      },
      error:(error) => {             
        this.generalService.getErrorMsg(error.error.message);
      }
    });
  }
  onCheckboxChange(e: any) {
    const sdg_ids: FormArray = this.sdgDetails.get('sdg_ids') as FormArray;
    if (e.target.checked) {
      sdg_ids.push(new FormControl(e.target.value));
      for(let sdgOptionKey in this.sdgOption){
        if(this.sdgOption[sdgOptionKey]['id']==e.target.value){
          this.sdgOption[sdgOptionKey]['selected']=true;
        }            
      }
    } else { 
      let i: number = 0;
      sdg_ids.controls.forEach((item: any) => {
        if (item.value == e.target.value) {
          for(let sdgOptionKey in this.sdgOption){
            if(this.sdgOption[sdgOptionKey]['id']==item.value){
              this.sdgOption[sdgOptionKey]['selected']=false;
            }            
          }          
          sdg_ids.removeAt(i);
          return;
        }
        i++;
      });
    }
  }
  generateSDGFormObj() {
    this.sdgDetails = this.formBuilder.group({
      sdg_ids: this.formBuilder.array([], [Validators.required]),
    });
    let sdg_ids: FormArray = this.sdgDetails.get('sdg_ids') as FormArray;
    this.sdgOption=[];
    for (let sdg of this.sdgList) {
      let isSdgExist=false;
      for (let userSdg of this.userData.sdg_ids) {
        if(sdg.id == userSdg.sdg_id){
          isSdgExist=true;
        }
      }
      if(isSdgExist){
        sdg_ids.push(new FormControl(sdg.id));
        sdg.selected=true;
        this.sdgOption.push(sdg);
      }else{
        sdg.selected=false;
        this.sdgOption.push(sdg)
      }
    }
  }
  generateInterestFormObj(){
    this.interestDetails = this.formBuilder.group({
      interest_ids: this.formBuilder.array([], [Validators.required]),
    });
    let interest_ids: FormArray = this.interestDetails.get('interest_ids') as FormArray;
    this.interestOption=[];
    for (let interest of this.interestList) {
      let isInterestExist=false;
      for (let userInterest of this.userData.interest_ids) {
        if(interest.id == userInterest.interest_id){
          isInterestExist=true;
        }
      }
      if(isInterestExist){
        interest_ids.push(new FormControl(interest.id));
        interest.selected=true;
        this.interestOption.push(interest);
      }else{
        interest.selected=false;
        this.interestOption.push(interest)
      }
    }
  }
  generatePersonalInfoFormObj() {
      this.personalDetails = this.formBuilder.group({
        name: [this.userData.name, [Validators.required,Validators.maxLength(35)]],
        email: [this.userData.email,[Validators.required, Validators.email]],
        dob: [this.userData.dob,Validators.required],
        location: [this.userData.location,[Validators.required,Validators.maxLength(55)]],
        contact_number: [this.userData.contact_number,[Validators.required,Validators.pattern("^[0-9]{10,11}")]],
        //sponsor_type: [this.userData.sponsor_type,Validators.required],
        //terms_condition: ['',Validators.required],
        phone_code: [this.userData.phone_code,Validators.required],
      });
  }

  generateCorpoInfoFormObj(){
    this.corporatDetails = this.formBuilder.group({
      corporation_name: [this.userData.corporation_name, [Validators.required,Validators.maxLength(100)]],
      industry: [
          (this.userData.industry != 'other') ? this.userData.industry_id : this.userData.industry,
          [Validators.required,Validators.maxLength(100)]],
      country: [this.userData.country,[Validators.required,Validators.maxLength(50)]],
      city: [this.userData.city,[Validators.required,Validators.maxLength(50)]],
      contact_name: [this.userData.contact_name,[Validators.required,Validators.maxLength(50)]],
      position: [this.userData.position,[Validators.required,Validators.maxLength(30)]],
      other_industry:[this.userData.other_industry,[]]
  });
  }
  generateSocialFormObj(){
    this.socialDetails = this.formBuilder.group({
      twitter: [this.userData.twitter],
      facebook: [this.userData.facebook],
      linkedin: [this.userData.linkedin],
      instagram: [this.userData.instagram],
      snapchat: [this.userData.snapchat],
      tiktok: [this.userData.tiktok]
    });
    
  }
  generateProfileImageFormObj() {
    this.profileImage = this.formBuilder.group({
      image: [""]
    });
  }
  get social() { return this.socialDetails.controls; }
  get aboutControl() { return this.aboutEdit.controls; }

  get sdgControl() { return this.sdgDetails.controls; }

  get userInterest() { return this.interestDetails.controls; }

  get personal() { return this.personalDetails.controls; }

  get corpo() { return this.corporatDetails.controls; }

  get pi() { return this.profileImage.controls; }
  selectedFile: any;
  onFileSelected(event: any) {
    this.selectedFile = null;
    this.selectedFile = event.target.files[0] as File;
    if (this.selectedFile instanceof File) {
      if (this.selectedFile.size <= 2048000) {
        this.spinner.show();
        const formData = new FormData();
        formData.append('image', this.selectedFile, this.selectedFile.name);
        this.appSettingsService.updateProfileImage(formData).subscribe((res: any) => {
          if (res) {     
            if (res.success) {
              this.spinner.hide();
              let user = res.data.profileDetails;
              localStorage.setItem("image", user.profile_image);
              this.generalService.passData(user);
              this.profileImg = user.profile_image;
              this.toastr.success(res.message, "Success");
              // this.router.navigate(['/dashboard'])
            } else {
              this.spinner.hide();
              this.generalService.getErrorMsg(res.message);
            }
          }
          this.spinner.hide();
        },
        (error) => {             
          this.generalService.getErrorMsg(error.error.message);
        });
      } else {
        this.generalService.getErrorMsg('Upload image of less than 2MB!')
      }
    }
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
  profileImg: string = '';
  getProfileDetail() {
    this.appSettingsService.getProfileDetails().subscribe({
      next:(res) => {      
        if (res.success) {
          let profile = res.data.userDetails;
          this.profileImg = profile.profile_image;
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
        } else {
          this.toastr.error(res.message, "error");
        }

      },
      error:(error) => {             
        this.generalService.getErrorMsg(error.error.message);
      }
    });
  }

  getUserDetails(){
    this.spinner.show();
    this.appSettingsService.getUserDetails().subscribe((res) => {
      if (res.success) {
        this.userData = res.data.userDetails;
        if (this.userData.sponsor_type == '2') {
          this.isCorporateUser = true;
          this.sponser = 'Corporate sponsor';
        }else{
          this.isCorporateUser = false;
          this.sponser = 'Individual Sponsor';
        }
        this.generateSocialFormObj();
        this.age = this.transform(this.userData.dob);
        
        if (this.userData.industry == 'other') {
          this.isOtherIndustry = true;
        } else {
          this.isOtherIndustry = false;
        }
        this.getSdgsList();
        this.getCategoriesList();
      }
      this.spinner.hide();
    })
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

    this.appSettingsService.updateProfile(formData).subscribe({
      next:(res: any) => {
      if (res) {
        this.spinner.hide();
        if (res.success) {
          let user = res.data.profileDetails;
          localStorage.setItem("name", user.name);
          localStorage.setItem("image", user.profile_image);
          this.generalService.passData(user);
          this.toastr.success(res.message, "Success");
          this.router.navigate(['/dashboard'])
        } else {
          this.generalService.getErrorMsg(res.message);
        }
      }
      this.spinner.hide();
    },
    error:(error) => {             
      this.generalService.getErrorMsg(error.error.message);
    }
  });
  }

  transform(value: any, args?: any): any {
    if (!value) {
      return '';
    }
  
    const birthDate = new Date(value);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
  
    if (today.getMonth() < birthDate.getMonth() || 
      (today.getMonth() === birthDate.getMonth() && today.getDate() < birthDate.getDate())) {
      age--;
    }
  
    return age;
  }
  
  getCountriesList(){
    this.appSettingsService.getCountriesList().subscribe((res: any) => {
      if (res.success) {
        this.countries = res.data
      }
    })
  }

  // getCategoriesList(){
  //   this.appSettingsService.getCategoriesList().subscribe((res: any) => {
  //     if (res.success) {
  //       this.categoryList = res.data
  //     }
  //   })
  // }  

  // Save About field: Start
  isAboutEdit:boolean=false;
  isAboutSubmit:boolean=false;
  editForm(event:any) {
    this.isAboutEdit=true;
    this.generateAboutFormObj();
  }
  discardForm(event:any){
    this.isAboutEdit=false;
    this.isAboutSubmit = false;
  }
  saveForm(event:any){
    this.isAboutSubmit=true;
    if (this.aboutEdit.invalid) { return  }
    const formData = new FormData();
    formData.append('field_type','about')
    formData.append('about',this.aboutEdit.value.about)
    this.saveFormData(formData);
    this.isAboutEdit=false;
  }
  // Save About field: Ends

// Save SDG field: Start
  isSdgEdit:boolean=false;
  isSdgSubmit:boolean=false;
  editSDGForm(event:any) {
    this.isSdgEdit=true;
    this.generateSDGFormObj();
  }
  discardSDGForm(event:any){
    this.isSdgEdit=false;
    this.isSdgSubmit = false;
  }
  formData = new FormData();
  mergeArray(formArray:any){
    
    Object.keys(formArray).forEach(key => {
      if(this.formData.has(key)===false){
        this.formData.append(key, formArray[key]);
      }else{
        this.formData.set(key, formArray[key]);
      }
    }) 
  }
  saveSDGForm(event:any){
    this.isSdgSubmit=true;
    if (this.sdgDetails.invalid) { return  }
    const formData = new FormData();
    formData.append('field_type','sdg_ids');
    let formArray= this.sdgDetails.value;
    Object.keys(formArray).forEach(key => {
      if(formData.has(key)===false){
        formData.append(key, formArray[key]);
      }else{
        formData.set(key, formArray[key]);
      }
    }) 
    this.saveFormData(formData);
    this.isSdgEdit=false;
  }
// Save SDG field: End

  // Save Project Types: Start
  getCategoriesList(){
    this.appSettingsService.getCategoriesList().subscribe({
      next:(res:any) => {
        this.interestList = res.data;
        this.generateInterestFormObj();
      },
      error:(error) => {             
        this.generalService.getErrorMsg(error.error.message);
      }
    });
  }

  isInterestEdit:boolean=false;
  isInterestSubmit:boolean=false;

  editInterestForm(event:any) {
    this.isInterestEdit=true;
    this.generateInterestFormObj();
  }
  discardInterestForm(event:any){
    this.isInterestEdit=false;
    this.isInterestSubmit = false;
  }

  saveInterestForm(event:any){
    this.isInterestSubmit=true;
    if (this.interestDetails.invalid) { return  }    
    const formData = new FormData();
    formData.append('field_type','interest_ids');
    let formArray= this.interestDetails.value;
    Object.keys(formArray).forEach(key => {
      if(formData.has(key)===false){
        formData.append(key, formArray[key]);
      }else{
        formData.set(key, formArray[key]);
      }
    })
    this.saveFormData(formData);
    this.isInterestEdit=false;
  }

  onInterestCheckboxChange(e: any) {
    const interest_ids: FormArray = this.interestDetails.get('interest_ids') as FormArray;
    if (e.target.checked) {
      interest_ids.push(new FormControl(e.target.value));
      for(let interestOptionKey in this.interestOption){
        if(this.interestOption[interestOptionKey]['id']==e.target.value){
          this.interestOption[interestOptionKey]['selected']=true;
        }            
      }
    } else { 
      let i: number = 0;
      interest_ids.controls.forEach((item: any) => {
        if (item.value == e.target.value) {
          for(let interestOptionKey in this.interestOption){
            if(this.interestOption[interestOptionKey]['id']==item.value){
              this.interestOption[interestOptionKey]['selected']=false;
            }            
          }          
          interest_ids.removeAt(i);
          return;
        }
        i++;
      });
    }
  }
  // Save Project Types: End


  // Save Personal Info: Start
  isPersonalEdit:boolean=false;
  isPersonalSubmit:boolean=false;
  editPersonalForm(event:any) {
    this.isPersonalEdit=true;
    this.generatePersonalInfoFormObj();
  }
  discardPersonalForm(event:any){
    this.isPersonalEdit=false;
    this.isPersonalSubmit = false;
  }
  savePersonalForm(event:any){    
    this.isPersonalSubmit=true;
    if (this.personalDetails.invalid) { return  }
    const formData = new FormData();
    formData.append('field_type','personal_info')
    formData.append('name',this.personalDetails.value.name)
    formData.append('dob',this.personalDetails.value.dob)
    formData.append('location',this.personalDetails.value.location)
    formData.append('contact_number',this.personalDetails.value.contact_number)
    formData.append('phone_code',this.personalDetails.value.phone_code)
    this.saveFormData(formData);
    this.isPersonalEdit=false;
  }
  // Save Personal Info: End

// Save Coprorate Info: Start
  isCorpoEdit:boolean=false;
  isCorpoSubmit:boolean=false;
  editCorpoForm(event:any) {
    this.isCorpoEdit=true;
    this.generateCorpoInfoFormObj();
  }
  discardCorpoForm(event:any){
    this.isCorpoEdit=false;
    this.isCorpoSubmit = false;
  }
  saveCorpoForm(event:any){    
    this.isCorpoSubmit=true;
    const otherIndustryControl = this.corporatDetails.get('other_industry');
    if (this.isOtherIndustry && otherIndustryControl != null) {
      otherIndustryControl.setValidators([Validators.required]);
    } else {
      otherIndustryControl?.clearValidators();
    }
    otherIndustryControl?.updateValueAndValidity();

    if (this.corporatDetails.invalid) { return  }
    const formData = new FormData();
    formData.append('field_type','corpo_info')
    formData.append('corporation_name',this.corporatDetails.value.corporation_name)
    formData.append('industry',this.corporatDetails.value.industry)
    formData.append('country',this.corporatDetails.value.country)
    formData.append('city',this.corporatDetails.value.city)
    formData.append('contact_name',this.corporatDetails.value.contact_name)
    formData.append('position',this.corporatDetails.value.position)
    formData.append('other_industry',this.corporatDetails.value.other_industry)
    this.saveFormData(formData);
    this.isCorpoEdit=false;
  }

// Save Corporate Info: End
  saveFormData(postData:any){
    this.spinner.show();
    this.appSettingsService.updateUserDetails(postData).subscribe((res) =>{
      if (res.success) {
        if (postData.get('field_type') == "personal_info") {
          localStorage.setItem('name', postData.get('name'))
          window.location.reload();
        }
        this.getUserDetails();
        this.toastr.success(res.message, "Success");
        this.spinner.hide();
      }else{
        this.generalService.getErrorMsg(res.message);
        this.spinner.hide();
      }
    })
    
  }

  // Save social Info: End
  isSocialEdit:boolean=false;
  isSocialSubmit:boolean=false;
  editSocialForm(event:any) {
    this.isSocialEdit=true;
    this.generateSocialFormObj();
  }
  discardSocialForm(event:any){
    this.isSocialEdit=false;
    this.isSocialSubmit = false;
  }
  saveSocialForm(event:any){    
    this.isSocialSubmit=true;
    if (this.socialDetails.invalid) { return  }
    const formData = new FormData();
    formData.append('field_type','social')
    formData.append('facebook',this.socialDetails.value.facebook)
    formData.append('twitter',this.socialDetails.value.twitter)
    formData.append('linkedin',this.socialDetails.value.linkedin)
    formData.append('instagram',this.socialDetails.value.instagram)
    formData.append('snapchat',this.socialDetails.value.snapchat)
    formData.append('tiktok',this.socialDetails.value.tiktok)
    this.saveFormData(formData);
    this.isSocialEdit=false;
  }
  // Save social Info: End
  industryList:any = [];
  getIndustryList(){
    this.appSettingsService.getIndustryList().subscribe({
      next:(res:any) => {
        if(res.success){
          this.industryList = res.data;  
        } else {
          this.toastr.success(res.message, "error");
        }
      },
      error:(error) => {
        this.router.navigate(["login"]);
      }
    });
  }

  checkOtherIndustry(event:any){
    const selectedOption = event.target.value
    if  (selectedOption == 'other'){
      this.isOtherIndustry = true;
      
    } else {
      this.isOtherIndustry = false;
      this.corporatDetails.controls['other_industry'].setValue('');
    }
  }
}
