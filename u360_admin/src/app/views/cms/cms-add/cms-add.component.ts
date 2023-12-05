import { Component, OnInit } from '@angular/core';
import { AbstractControl, UntypedFormBuilder, UntypedFormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import { ApiOperationManagerService } from 'src/app/services/api/operation-manager/api/api-operation-manager.service';
import { GeneralService } from 'src/app/services/general.service';

@Component({
  selector: 'app-cms-add',
  templateUrl: './cms-add.component.html',
  styleUrls: ['./cms-add.component.scss']
})
export class CmsAddComponent implements OnInit {

  isSubmitted = false;
  cmsAdd: UntypedFormGroup = new UntypedFormGroup({});

  constructor(
    private apiService: ApiOperationManagerService,
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private formBuilder: UntypedFormBuilder,
    public router: Router,
    public generalService: GeneralService,
    private appSettingsService: AppSettingsService,
  ) { }

  ngOnInit(): void {
    this.generateFormObj();
  }

  generateFormObj() {
    this.cmsAdd = this.formBuilder.group({
      name: ["", Validators.required],
      slug: ["", Validators.required],
      content: ["", Validators.required],
      status: ["", Validators.required]
    });
  }

  get f(): { [key: string]: AbstractControl } {
    return this.cmsAdd.controls;
  }

  onSubmit(): void {
    this.isSubmitted = true;
    if (this.cmsAdd.invalid) {
      return;
    }
    this.spinner.show();
    this.cmsAdd.value.status = this.cmsAdd.value.status == true ? "1" : "0",
      this.appSettingsService.createCms(this.cmsAdd.value).subscribe((res: any) => {
        if (res) {
          this.spinner.hide();
          if (res.success == true) {
            this.toastr.success(res.message, "Success");
            this.router.navigate(['/cms-management']);
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
