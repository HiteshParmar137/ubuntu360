import { Component, OnInit } from '@angular/core';
import { AbstractControl, UntypedFormBuilder, UntypedFormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import { ApiOperationManagerService } from 'src/app/services/api/operation-manager/api/api-operation-manager.service';
import { GeneralService } from 'src/app/services/general.service';
import { CKEditorComponent } from 'ckeditor4-angular/ckeditor.component';

@Component({
  selector: 'app-cms-edit',
  templateUrl: './cms-edit.component.html',
  styleUrls: ['./cms-edit.component.scss']
})
export class CmsEditComponent implements OnInit {

  id: any;
  cmsEdit: UntypedFormGroup = new UntypedFormGroup({});
  isSubmitted = false;
  responseFlag: boolean = false;
  noDataFlag: string = "";
  finalResponse: any[] = [];
  isSuperUser: boolean = false;

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
  editorConfig = {
    allowedContent: true,
    extraAllowedContent: '*(*);*{*}',
  };

  ngOnInit(): void {
    this.getSystemModule();
    this.getCmsDetail();
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

  getCmsDetail() {
    this.appSettingsService.getCmsDetails(this.id).subscribe((res) => {
      if(res.success== true){
        let cmsDate = res.data.cmsDetails;
        if (cmsDate) {
          this.cmsEdit = this.formBuilder.group({
            id: [this.id],
            name: [cmsDate.name, Validators.required],
            slug: [cmsDate.slug, Validators.required],
            content: [cmsDate.content, Validators.required],
            status: [cmsDate.status=='1'? true : false],
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

  generateFormObj() {
    this.cmsEdit = this.formBuilder.group({
      id: [""],
      name: ["", Validators.required],
      slug: ["", Validators.required],
      content: ["", Validators.required],
      status: [""],
      template_type: ["", Validators.required],
    });
  }

  get f(): { [key: string]: AbstractControl } {
    return this.cmsEdit.controls;
  }

  onSubmit(): void {
    this.isSubmitted = true;
    
    if (this.cmsEdit.invalid) {
      return;
    }
    this.spinner.show();
    this.cmsEdit.value.status=this.cmsEdit.value.status == true ? "1" : "0",
    this.appSettingsService.updateCms(this.cmsEdit.value).subscribe((res:any) => {
      if (res) {
        this.spinner.hide();
        if(res.success==true){
          this.toastr.success(res.message, "Success");
          this.router.navigate(['/cms-management']);
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
