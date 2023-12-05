import { Component, AfterViewInit } from '@angular/core';
import { FooterComponent } from '@coreui/angular';
import {
  AbstractControl,
  UntypedFormBuilder,
  UntypedFormGroup,
  Validators,
} from '@angular/forms';
import { Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { NgxSpinnerService } from 'ngx-spinner';
import { AppSettingsService } from "src/app/app-settings.service";
import { GeneralService } from 'src/app/services/general.service';

@Component({
  selector: 'app-default-without-login-footer',
  templateUrl: './default-without-login-footer.component.html',
  styleUrls: ['./default-without-login-footer.component.scss'],
})
export class DefaultWithoutLoginFooterComponent extends FooterComponent implements AfterViewInit {
  subscriptionForm: UntypedFormGroup = new UntypedFormGroup({});
  submitted = false;
  phone_no:any;
  email:any;
  whatsapp_no:any;
  instagram:any;
  twitter:any;
  facebook:any;
  youtube:any;
  visit_us:any;
  siteSettingSubscriber: any;
  constructor(
    private formBuilder: UntypedFormBuilder,
    public router: Router,
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private appSettingsService: AppSettingsService,
    private generalService: GeneralService
  ) {
    super();
    this.siteSettingSubscriber = this.generalService.siteSettingsDataListener.subscribe((data: any) => {
      this.phone_no = data.phone_no ?? '';
      this.email = data.email ?? '';
      this.whatsapp_no = data.whatsapp_no ?? '';
      this.twitter = data.twitter ?? '';
      this.instagram = data.instagram ?? '';
      this.facebook = data.facebook ?? '';
      this.youtube = data.youtube ?? '';
      this.visit_us = data.visit_us ?? '';
    });
  }

  ngOnInit(): void {
    this.generateFormObj();
    
  }
  ngAfterViewInit() {
    this.phone_no= localStorage.getItem('phone_no') ?? '';
    this.email= localStorage.getItem('email') ?? '';
    this.whatsapp_no= localStorage.getItem('whatsapp_no') ?? '';
    this.twitter= localStorage.getItem('twitter') ?? '';
    this.instagram= localStorage.getItem('instagram') ?? '';
    this.facebook= localStorage.getItem('facebook') ?? '';
    this.youtube= localStorage.getItem('youtube') ?? '';
    this.visit_us= localStorage.getItem('visit_us') ?? '';
  }
  get f(): { [key: string]: AbstractControl } {
    return this.subscriptionForm.controls;
  }

  generateFormObj() {
    this.subscriptionForm = this.formBuilder.group({
      email: ['', [Validators.required, Validators.email]],
    });
  }

  subscribe() {
    this.submitted = true;
    if (this.subscriptionForm.invalid) {
      return;
    }
    this.appSettingsService.saveSubscribeEmail(this.subscriptionForm.value).subscribe((res: any) => {
      if (res) {
        if (res.success == true) {
          this.toastr.success(res.message, "Success");
        } else {
          this.generalService.getErrorMsg(res.message);
        }
      }
    });
  }
}
