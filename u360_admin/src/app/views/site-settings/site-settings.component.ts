import { Component, OnInit } from '@angular/core';
import { AbstractControl, UntypedFormBuilder, UntypedFormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import { ApiOperationManagerService } from 'src/app/services/api/operation-manager/api/api-operation-manager.service';
import { GeneralService } from 'src/app/services/general.service';

@Component({
  selector: 'app-site-settings',
  templateUrl: './site-settings.component.html',
  styleUrls: ['./site-settings.component.scss']
})
export class SiteSettingsComponent implements OnInit {
  settingForm: UntypedFormGroup = new UntypedFormGroup({});
  isSubmitted = false;
  responseFlag: boolean = false;
  noDataFlag: string = "";
  finalResponse: any[] = [];
  isSuperUser: boolean = false;
  search: string = "";
  //settingArray:any={'SITE_LOGO':'Site Logo','SITE_NAME':'Site Name'};
  settingArray: any[] = [{ 'key': 'phone_no', 'value': 'Phone No' }, { 'key': 'email', 'value': 'Email' }, { 'key': 'whatsapp_no', 'value': 'WhatsApp No' }, { 'key': 'twitter', 'value': 'Twitter' }, { 'key': 'instagram', 'value': 'Instagram' }, { 'key': 'facebook', 'value': 'Facebook' }, { 'key': 'youtube', 'value': 'Youtube' }, { 'key': 'visit_us', 'value': 'Visit Us' }];
  formArray: any[] = [];
  constructor(
    private apiService: ApiOperationManagerService,
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private formBuilder: UntypedFormBuilder,
    private route: ActivatedRoute,
    public router: Router,
    public generalService: GeneralService,
    private appSettingsService: AppSettingsService
  ) {
  }
  ngOnInit(): void {
    this.getSystemModule();
    this.getSiteSettingsDetails();
    this.generateFormObj();
  }
  getSystemModule() {
    this.spinner.show();
    this.appSettingsService.getModulesJSON().subscribe((data) => {
      this.responseFlag = true;
      this.finalResponse = data;
      this.spinner.hide();
    },
    (error) => {     
      this.spinner.hide();        
      this.generalService.getErrorMsg(error.error.message);
    });
  }
  getSiteSettingsDetails() {
    this.appSettingsService.getSiteSettingsDetails(this.search).subscribe((res) => {
      if (res.success == true) {
        let cmsDate = res.data.siteSettings;
        if (cmsDate) {
          for (let obj of cmsDate) {
            for (let key in obj) {
              if (key == 'key') {
                for (let settingObj of this.settingArray) {
                  if (settingObj.key == obj[key]) {
                    this.formArray[settingObj.key] = [obj['value']];
                  }
                }
              }
            }
          }
          this.settingForm = this.formBuilder.group(this.formArray);
        }
        this.toastr.success(res.message, "Success");
      } else {
        this.toastr.success(res.message, "error");
      }
    },
    (error) => {     
      this.spinner.hide();        
      this.generalService.getErrorMsg(error.error.message);
    });
  }

  generateFormObj() {
    for (let settingObj of this.settingArray) {
      //this.formArray.push({[settingObj.key]:['']});
      this.formArray[settingObj.key] = [''];
    }
    console.log(this.formArray);
    this.settingForm = this.formBuilder.group(this.formArray);
  }

  get f(): { [key: string]: AbstractControl } {
    return this.settingForm.controls;
  }

  onSubmit(): void {
    this.isSubmitted = true;

    if (this.settingForm.invalid) {
      return;
    }
    this.spinner.show();
    this.appSettingsService.saveSiteSettings(this.settingForm.value).subscribe((res: any) => {
      if (res) {
        this.spinner.hide();
        if (res.success == true) {
          this.toastr.success(res.message, "Success");
        } else {
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
