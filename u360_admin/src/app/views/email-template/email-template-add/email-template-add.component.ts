import { Component, OnInit } from '@angular/core';
import { AbstractControl, UntypedFormBuilder, UntypedFormGroup, Validators,FormControl } from '@angular/forms';
import { Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import { ApiOperationManagerService } from 'src/app/services/api/operation-manager/api/api-operation-manager.service';
import { GeneralService } from 'src/app/services/general.service';
@Component({
  selector: 'app-email-template-add',
  templateUrl: './email-template-add.component.html',
  styleUrls: ['./email-template-add.component.scss']
})
export class EmailTemplateAddComponent implements OnInit {
  isSubmitted = false;
  emailTemplateAdd: UntypedFormGroup = new UntypedFormGroup({});
  templateValue: string = '';
  constructor(
    private apiService: ApiOperationManagerService,
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private formBuilder: UntypedFormBuilder,
    public router: Router,
    public generalService: GeneralService,
    private appSettingsService: AppSettingsService
  ) { }

  ngOnInit(): void {
    this.generateFormObj();
  }

  generateFormObj() {
    this.emailTemplateAdd = this.formBuilder.group({
      name: ["", Validators.required],
      slug: ["", Validators.required],
      template: ["", Validators.required],
      status: ["", Validators.required],
      template_type: ["", Validators.required],
    });
  }

  get f(): { [key: string]: AbstractControl } {
    return this.emailTemplateAdd.controls;
  }

  onSubmit(): void {
    this.isSubmitted = true;
    if (this.emailTemplateAdd.invalid) {
      return;
    }
    this.spinner.show();
    this.emailTemplateAdd.value.status = this.emailTemplateAdd.value.status == true ? "1" : "0",
      this.appSettingsService.createTemplate(this.emailTemplateAdd.value).subscribe((res: any) => {
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
    if (type && type === 'Email') {
      this.templateValue = 'Email';
      this.emailTemplateAdd.addControl('subject', new FormControl('', Validators.required));
    } else {
      this.templateValue = 'SMS';
      this.emailTemplateAdd.removeControl('subject');
    }
  }
}
