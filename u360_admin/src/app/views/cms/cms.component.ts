import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import { AbstractControl, UntypedFormBuilder, UntypedFormGroup, Validators } from '@angular/forms';
import { GeneralFunction } from "src/app/_directives/general-function.directive";
import { GeneralService } from 'src/app/services/general.service';

@Component({
  selector: 'app-cms',
  templateUrl: './cms.component.html',
  styleUrls: ['./cms.component.scss']
})
export class CmsComponent implements OnInit {
  isSubmitted = false;
  finalResponse: [] = [];
  responseFlag: boolean = false;
  noDataFlag: string = "";
  spinnerShow: string = "Yes";
  page: number = 1;
  total: number = 0;
  per_page: number = 0;
  filterForm: UntypedFormGroup = new UntypedFormGroup({});
  name: string = '';
  template_type: string = '';
  status: string = '';
  current_page: any = 1;
  limit: any;
  offset: any;
  perPageRecord = [];
  pager: any = {};
  index: number = 0;
  start_record: any;

  constructor(
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private appSettingsService: AppSettingsService,
    public router: Router,
    private formBuilder: UntypedFormBuilder,
    private generalService: GeneralService
  ) { }

  ngOnInit(): void {
    this.getCmsList();
  }

  reloadCmsList () {
    this.spinner.show();
    this.getCmsList();
    this.spinner.hide();
  }
  getCmsList() {
    GeneralFunction.setAuthorizeModule('cms-management', 'list');
    if (this.name == '') {
      this.spinner.show();
    }
    const postData = { 'page': this.current_page, 'name': this.name, 'template_type': this.template_type, 'status': this.status };
    this.appSettingsService.getCmsList(postData).subscribe((res) => {
      if (res.success === true) {
        this.responseFlag = true;
        if (res.data && res.data.response && res.data.response.length > 0) {
          this.index = (this.current_page - 1) * res.data.pagination.per_page + 1;
          this.limit = res.data.pagination.per_page;
          this.finalResponse = [];
          let currentPaginationObject = {
            range: this.current_page,
            total: res.data.pagination.total,
          };
          this.pager = GeneralFunction.setPagination(
            currentPaginationObject,
            this.current_page,
            this.limit
          );
          this.finalResponse = res.data.response;
        } else {
          this.finalResponse = [];
          this.noDataFlag = "Data Not Found";
        }
      } else {
        this.finalResponse = [];
        this.noDataFlag = "Data Not Found";
      }
      this.spinner.hide();
    },
    (error) => {     
      this.spinner.hide();        
      this.generalService.getErrorMsg(error.error.message);
    });
  }
 
  
  setCurrent(pageNumber: number) {
    if (pageNumber > 0 && $.inArray(pageNumber, this.pager.pages) !== -1) {
      this.current_page = pageNumber;
      this.offset = (pageNumber - 1) * this.limit;
      this.start_record = this.offset + 1;
      this.getCmsList();
    }
  }
  async deleteEmailTemplate(id: string) {
    this.spinner.show();
    // let confirmation = await this.generalService.openDeleteConfirmationDialog({title: 'Are you sure?',text:'You want to delete this template?'});
    let deleteArray = {title: 'Are you sure?',text:'Do you want to delete this template?'};
    this.generalService.openDeleteConfirmationDialog(deleteArray).then(response => {
      if(response){
        this.appSettingsService.deleteCms(id).subscribe((res: any) => {
          if (res) {
            this.spinner.hide();
            if (res.success == true) {
              this.toastr.success(res.message, "Success");
              this.getCmsList();
            } else {
              this.toastr.error(res.message, "error");
            }
          }
          this.spinner.hide();
        },
        (error) => {     
          this.spinner.hide();        
          this.generalService.getErrorMsg(error.error.message);
        });
      }
    });
    this.spinner.hide();
  }
  searchData() {
    this.current_page = 1;
    // this.name = (<HTMLInputElement>document.getElementById('name')).value;
    // this.template_type = (<HTMLInputElement>document.getElementById('template_type')).value;
    // this.status = (<HTMLInputElement>document.getElementById('status')).value;
    this.getCmsList();
  }
  resetData() {
    this.name = '';
    this.template_type = '';
    this.status = '';
    this.getCmsList();
  }

}
