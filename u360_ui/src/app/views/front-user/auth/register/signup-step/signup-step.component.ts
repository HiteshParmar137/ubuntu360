import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { AppSettingsService } from 'src/app/app-settings.service';
import { Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { NgxSpinnerService } from 'ngx-spinner';
import { FormArray, FormControl, UntypedFormBuilder, UntypedFormGroup, Validators } from "@angular/forms";
import { Options } from '@angular-slider/ngx-slider';
import { GeneralService } from 'src/app/services/general.service';
import * as $ from 'jquery';
import 'bootstrap';
import 'select2';
import 'bootstrap-datepicker';
import 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.css';
import 'node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js';
@Component({
  selector: 'app-signup-step',
  templateUrl: './signup-step.component.html',
  styleUrls: ['./signup-step.component.scss']
})
export class SignupStepComponent implements OnInit {
  Data: Array<any> = [
    { name: 'Pear', value: 'pear' },
    { name: 'Plum', value: 'plum' },
    { name: 'Kiwi', value: 'kiwi' },
    { name: 'Apple', value: 'apple' },
    { name: 'Lime', value: 'lime' }
  ];
  formData = new FormData();
  userDetails: any = [];
  sdgsList: any = [];
  interestList: any = [];
  personalDetails: UntypedFormGroup = new UntypedFormGroup({});
  corporatDetails: UntypedFormGroup = new UntypedFormGroup({});
  sdgDetails: UntypedFormGroup = new UntypedFormGroup({});
  interestDetails: UntypedFormGroup = new UntypedFormGroup({});
  socialDetails: UntypedFormGroup = new UntypedFormGroup({});
  personal_step = false;
  corporat_step = false;
  sdg_step = false;
  interest_step = false;
  social_step = false;
  step = 1;
  isSubmitted = false;
  personalDetailsStep: boolean = true;
  isOtherIndustry: boolean = false;
  options: Options = {
    showTicksValues: true,
    showSelectionBarFromValue: 0,
    stepsArray: [
      { value: 1, legend: "Not at all" },
      { value: 2, legend: "Rarely" },
      { value: 3, legend: "Regular" },
    ]
  };
  sdgOption: any = [];
  interestOption: any = [];
  inputValue: string = '';
  capitalizedValue: string = '';
  industryList: any = [];
  @ViewChild('countryPhoneCode') countryPhoneCodeRef!: ElementRef;
  @ViewChild('countryList') countryListRef!: ElementRef;
  constructor(
    private formBuilder: UntypedFormBuilder,
    private apiServvice: AppSettingsService,
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    public generalService: GeneralService,
    public router: Router,
  ) {

  }
  ngOnInit() {

    if (localStorage.getItem('token') == '') {
      this.router.navigate(["login"]);
    }
    this.getUserDetails();
    this.generateFormObj('all');
    this.getCountryCodeList();

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
  getSdgsDetails() {
    this.apiServvice.getSdgsList().subscribe({
      next: (res: any) => {
        this.sdgsList = res.data;
        this.generateFormObj('sdg');
      },
      error: (error) => {
          this.generalService.getErrorMsg(error.error.message);
      }
    });
  }
  getCategoriesList() {
    this.apiServvice.getCategoriesList().subscribe({
      next:(res: any) => {
        this.interestList = res.data;
        this.generateFormObj('interest');
      },
      error:(error) => {
        this.generalService.getErrorMsg(error.error.message);
      }
    });
  }
  countryList: any = [];
  getCountryCodeList() {
    this.apiServvice.getCountriesList().subscribe({
      next:(res: any) => {
        this.countryList = res.data;
      },
      error:(error) => {
        this.generalService.getErrorMsg(error.error.message);
      }
    });
  }
  getUserDetails(type: any = '', step: any = '') {
    this.apiServvice.getUserDetails().subscribe({
      next:(res: any) => {
        if (res.success) {
          this.userDetails = res.data.userDetails;
          if (this.userDetails.other_industry != '') {
            this.isOtherIndustry = true;
          } else {
            this.isOtherIndustry = false;
          }
          if (type == '') {
            this.generateFormObj('personalDetails');
            this.getSdgsDetails();
            this.getCategoriesList();
            this.getIndustryList();
          }

          if (step == 'social') {
            this.generateFormObj('social');
          }
        } else {
          this.toastr.success(res.message, "error");
          this.router.navigate(["login"]);
        }

      }, 
      error:(error) => {                              //Error callback
        this.router.navigate(["login"]);
      }
    });
  }
  generateFormObj(formType: any) {
    if (formType == 'personalDetails' || formType == 'all') {
      const currentDate = new Date();
      this.personalDetails = this.formBuilder.group({
        name: [this.userDetails.name, [Validators.required, Validators.maxLength(35), Validators.pattern('^[a-zA-Z ]*$')]],
        email: [this.userDetails.email, [Validators.required, Validators.email]],
        dob: [this.userDetails.dob, Validators.required],
        location: [this.userDetails.location, [Validators.required, Validators.maxLength(55), Validators.pattern('^[a-zA-Z0-9!@#\$%\^\&*\)\(+=._-]+$')]],
        contact_number: [this.userDetails.contact_number, [Validators.required, Validators.pattern("^[0-9]{6,11}")]],
        sponsor_type: [this.userDetails.sponsor_type, Validators.required],
        terms_condition: ['', Validators.required],
        phone_code: [this.userDetails.phone_code, Validators.required]
      });
      this.corporatDetails = this.formBuilder.group({
        corporation_name: [this.userDetails.corporation_name, [Validators.required, Validators.maxLength(100)]],
        industry: [this.userDetails.industry_id, [Validators.required, Validators.maxLength(100)]],
        country: [this.userDetails.country, [Validators.required, Validators.maxLength(50)]],
        city: [this.userDetails.city, [Validators.required, Validators.maxLength(50)]],
        contact_name: [this.userDetails.contact_name, [Validators.required, Validators.maxLength(50), Validators.pattern("[a-zA-Z][a-zA-Z ]+")]],
        position: [this.userDetails.position, [Validators.required, Validators.maxLength(30)]],
        other_industry: [this.userDetails.other_industry, []]
      });
    }
    if (formType == 'personalDetails' || formType == 'social' || formType == 'all') {
      this.socialDetails = this.formBuilder.group({
        twitter: [this.userDetails.twitter],
        facebook: [this.userDetails.facebook],
        linkedin: [this.userDetails.linkedin],
        instagram: [this.userDetails.instagram],
        snapchat: [this.userDetails.snapchat],
        tiktok: [this.userDetails.tiktok]
      });
    }
    if (formType == 'sdg' || formType == 'all') {
      this.sdgDetails = this.formBuilder.group({
        sdg_ids: this.formBuilder.array([], [Validators.required]),
      });
      let sdg_ids: FormArray = this.sdgDetails.get('sdg_ids') as FormArray;

      for (let sdg of this.sdgsList) {
        let isSdgExist = false;
        for (let userSdg of this.userDetails.sdg_ids) {
          if (sdg.id == userSdg.sdg_id) {
            isSdgExist = true;
          }
        }
        if (isSdgExist) {
          sdg_ids.push(new FormControl(sdg.id));
          sdg.selected = true;
          this.sdgOption.push(sdg);
        } else {
          sdg.selected = false;
          this.sdgOption.push(sdg)
        }
      }
    }

    if (formType == 'interest' || formType == 'all') {
      this.interestDetails = this.formBuilder.group({
        interest_ids: this.formBuilder.array([], [Validators.required]),
      });
      let interest_ids: FormArray = this.interestDetails.get('interest_ids') as FormArray;

      for (let interest of this.interestList) {
        let isInterestExist = false;
        for (let userInterest of this.userDetails.interest_ids) {
          if (interest.id == userInterest.interest_id) {
            isInterestExist = true;
          }
        }
        if (isInterestExist) {
          interest_ids.push(new FormControl(interest.id));
          interest.selected = true;
          this.interestOption.push(interest);
        } else {
          interest.selected = false;
          this.interestOption.push(interest)
        }
      }
    }
  }
  get personal() { return this.personalDetails.controls; }

  get corporation() { return this.corporatDetails.controls; }

  get sdg() { return this.sdgDetails.controls; }

  get social() { return this.socialDetails.controls; }

  get userInterest() { return this.interestDetails.controls; }
  mergeArray(formArray: any) {
    Object.keys(formArray).forEach(key => {
      if (this.formData.has(key)) {
        this.formData.append(key, formArray[key]);
      } else {
        this.formData.set(key, formArray[key]);
      }
    })
  }
  next() {
    if (this.step == 1) {
      this.personal_step = true;
      if (this.personalDetails.invalid) { return }
      this.mergeArray(this.personalDetails.value);
      this.saveData(this.formData, 'next');
    }
    else if (this.step == 2) {
      this.corporat_step = true;
      const otherIndustryControl = this.corporatDetails.get('other_industry');
      if (this.isOtherIndustry && otherIndustryControl != null) {
        otherIndustryControl.setValidators([Validators.required]);
      } else {
        otherIndustryControl?.clearValidators();
      }

      otherIndustryControl?.updateValueAndValidity();
      if (this.corporatDetails.invalid) { return }
      this.mergeArray(this.corporatDetails.value);
      this.saveData(this.formData, 'next');
    }
    else if (this.step == 3) {
      this.sdg_step = true;
      if (this.sdgDetails.invalid) { return }
      this.mergeArray(this.sdgDetails.value);
      this.saveData(this.formData, 'next');
    }
    else if (this.step == 4) {
      this.social_step = true;
      if (this.socialDetails.invalid) { return }
      this.mergeArray(this.socialDetails.value);
      this.saveData(this.formData, 'next');
    }

  }
  skip(){
    if (this.step == 1) {
      this.personal_step = true;
      this.step++;
    }
    else if (this.step == 2) {
      this.corporat_step = true;
      this.step++;
    }
    else if (this.step == 3) {
      this.sdg_step = true;
      this.step++;
    }
    else if (this.step == 4) {
      this.social_step = true;
      this.step++;
    }
    else if (this.step == 5) {
      this.mergeArray(this.interestDetails.value);
      this.formData.append('is_skip', 'true');
      this.saveData(this.formData, 'submit');
    }
  }
  saveData(formValue: any, submitType: string) {
    this.apiServvice.saveUserDetails(formValue, this.step, submitType).subscribe({
      next : (res: any) => {
        if (res) {
          if (res.success) {
            this.toastr.success(res.message, "success");
            if (this.personalDetails.value.sponsor_type == 1 && this.step == 1) {
              this.step = 3;
            } else {
              this.step++;
            }
            if (this.step > 1) {
              this.personalDetailsStep = false;
            }
            if (submitType == 'submit') {
              localStorage.setItem("is_signup_completed", "1");
              this.router.navigate(["dashboard"]);
            }
          } else {
            let convertErrorMessage = Object.values(res.message);
            if (typeof res.message === 'object') {
              let convertArrayMessage: any[] = [];
              let i = 1;
              for (let convert of convertErrorMessage) {
                let c = i + '.' + convert + `</br>`;
                i++;
                convertArrayMessage.push(c);
              }
              if (convertArrayMessage.length === 1) {
                let singleMessage: any = Object.values(res.message)[0];
                this.toastr.error(singleMessage, 'Error');
              } else {
                let errorMessage = convertArrayMessage.join(' ');
                this.toastr.error(errorMessage, 'Error', {
                  enableHtml: true,
                });
              }
            } else {
              this.toastr.error(res.message, 'Error');
            }
          }
        }
      },
      error: (error) => {
        this.generalService.getErrorMsg(error.error.message);
      }
    });
  }
  previous() {
    if (this.personalDetails.value.sponsor_type == 1) {
      if (this.step == 3) {
        this.step = 1;

      } else {
        this.step--;
      }
    } else {
      this.step--;
    }
    this.getUserDetails('previous');
    if (this.step == 1) {
      this.corporat_step = false;
      this.personalDetailsStep = true;
    }
    if (this.step == 2) {
      this.sdg_step = false;
    }
    if (this.step == 4) {
      this.social_step = false;
      this.getUserDetails('previous', 'social');
    }

  }

  submit() {
    if (this.step == 5) {
      this.interest_step = true;
      if (this.interestDetails.invalid) { return }
      this.mergeArray(this.interestDetails.value);
      this.saveData(this.formData, 'submit');
    }
  }
  onCheckboxChange(e: any) {
    const sdg_ids: FormArray = this.sdgDetails.get('sdg_ids') as FormArray;
    if (e.target.checked) {
      sdg_ids.push(new FormControl(e.target.value));
      for (let sdgOptionKey in this.sdgOption) {
        if (this.sdgOption[sdgOptionKey]['id'] == e.target.value) {
          this.sdgOption[sdgOptionKey]['selected'] = true;
        }
      }
    } else {
      let i: number = 0;
      sdg_ids.controls.forEach((item: any) => {
        if (item.value == e.target.value) {
          for (let sdgOptionKey in this.sdgOption) {
            if (this.sdgOption[sdgOptionKey]['id'] == item.value) {
              this.sdgOption[sdgOptionKey]['selected'] = false;
            }
          }
          sdg_ids.removeAt(i);
          return;
        }
        i++;
      });
    }
  }

  onInterestCheckboxChange(e: any) {
    const interest_ids: FormArray = this.interestDetails.get('interest_ids') as FormArray;
    if (e.target.checked) {
      interest_ids.push(new FormControl(e.target.value));
      for (let interestOptionKey in this.interestOption) {
        if (this.interestOption[interestOptionKey]['id'] == e.target.value) {
          this.interestOption[interestOptionKey]['selected'] = true;
        }
      }
    } else {
      let i: number = 0;
      interest_ids.controls.forEach((item: any) => {
        if (item.value == e.target.value) {
          for (let interestOptionKey in this.interestOption) {
            if (this.interestOption[interestOptionKey]['id'] == item.value) {
              this.interestOption[interestOptionKey]['selected'] = false;
            }
          }
          interest_ids.removeAt(i);
          return;
        }
        i++;
      });
    }
  }
  onTermCheckboxChange(e: any) {
    if (e.target.checked) {
      this.personalDetails.controls['terms_condition'].setValue(true);
    } else {
      this.personalDetails.controls['terms_condition'].setValue('');
    }
  }

  capitalizeFirstLetter(event: any) {
    this.inputValue = event.target.value;
    if (this.inputValue != '') {
      event.target.value = this.inputValue.charAt(0).toUpperCase() + this.inputValue.slice(1);
    } else {
      event.target.value = '';
    }
  }

  getIndustryList() {
    this.apiServvice.getIndustryList().subscribe((res: any) => {
      if (res.success == true) {
        this.industryList = res.data;
      } else {
        //this.toastr.success(res.message, "error");
      }
    }, (error) => {
      this.router.navigate(["login"]);
    });
  }

  checkOtherIndustry(event: any) {
    const selectedOption = event.target.value
    if (selectedOption == 'other') {
      this.isOtherIndustry = true;

    } else {
      this.isOtherIndustry = false;
      this.corporatDetails.controls['other_industry'].setValue('');
    }
  }
}