import { Component, OnInit } from '@angular/core';
import { AbstractControl, UntypedFormBuilder, UntypedFormGroup, Validators, FormControl } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import { ApiOperationManagerService } from 'src/app/services/api/operation-manager/api/api-operation-manager.service';
import { GeneralService } from 'src/app/services/general.service';

@Component({
  selector: 'app-email-template-edit',
  templateUrl: './email-template-edit.component.html',
  styleUrls: ['./email-template-edit.component.scss']
})
export class EmailTemplateEditComponent implements OnInit {
  id: any;
  emailTemplateEdit: UntypedFormGroup = new UntypedFormGroup({});
  isSubmitted = false;
  responseFlag: boolean = false;
  noDataFlag: string = "";
  finalResponse: any[] = [];
  isSuperUser: boolean = false;
  templateValue: string = '';
  templateDate: any;
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
    this.route.paramMap.subscribe((params) => {
      this.id = params.get("id");
    });
  }

  ngOnInit(): void {
    this.getSystemModule();
    this.getEmailTemplateDetail();
    this.generateFormObj();
    if (this.id == 1) {
      this.isSuperUser = true;
    }
  }

  getSystemModule() {
    this.spinner.show();
    this.appSettingsService.getModulesJSON().subscribe((data) => {
      this.responseFlag = true;
      this.finalResponse = data;
      this.spinner.hide();
    });
  }

  getEmailTemplateDetail() {
    this.appSettingsService.getTemplateDetails(this.id).subscribe((res) => {
      if (res.success == true) {
        this.templateDate = res.data.templateDetails;
        if (this.templateDate) {
          this.emailTemplateEdit = this.formBuilder.group({
            id: [this.id],
            name: [this.templateDate.name, Validators.required],
            slug: [this.templateDate.slug, Validators.required],
            template: [this.templateDate.template, Validators.required],
            status: [this.templateDate.status == '1' ? true : false],
            template_type: [this.templateDate.template_type, Validators.required],
          });
          this.getTemplateType(this.templateDate.template_type);
          if (this.templateDate.template_type === 'email') {
            this.templateValue = 'email';
          } else {
            this.templateValue = 'sms';
          }
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
    this.emailTemplateEdit = this.formBuilder.group({
      id: [""],
      name: ["", Validators.required],
      slug: ["", Validators.required],
      template: ["", Validators.required],
      status: [""],
      template_type: ["", Validators.required],
    });
  }

  get f(): { [key: string]: AbstractControl } {
    return this.emailTemplateEdit.controls;
  }

  onSubmit(): void {
    this.isSubmitted = true;

    if (this.emailTemplateEdit.invalid) {
      return;
    }
    this.spinner.show();
    this.emailTemplateEdit.value.status = this.emailTemplateEdit.value.status == true ? "1" : "0",
      this.appSettingsService.updateTemplate(this.emailTemplateEdit.value).subscribe((res: any) => {
        if (res) {
          this.spinner.hide();
          if (res.success == true) {
            this.toastr.success(res.message, "Success");
            this.router.navigate(['/templates-management']);
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

  getTemplateType(type: any) {
    if (type && type === 'email') {
      this.templateValue = 'email';
      this.emailTemplateEdit.addControl('subject', new FormControl(this.templateDate.subject, Validators.required));
    } else {
      this.templateValue = 'sms';
      this.emailTemplateEdit.removeControl('subject');
    }
  }
}
