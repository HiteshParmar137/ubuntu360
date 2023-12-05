import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import { AbstractControl, UntypedFormBuilder, UntypedFormGroup, Validators } from '@angular/forms';
import { GeneralFunction } from "src/app/_directives/general-function.directive";
import { GeneralService } from 'src/app/services/general.service';

@Component({
  selector: 'app-email-template',
  templateUrl: './email-template.component.html',
  styleUrls: ['./email-template.component.scss']
})
export class EmailTemplateComponent implements OnInit {
  isSubmitted = false;
  finalResponse: [] = [];
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
  noDataFlag: string = "";
  responseFlag: boolean = false;

  constructor(
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private appSettingsService: AppSettingsService,
    public router: Router,
    private formBuilder: UntypedFormBuilder,
    private generalService: GeneralService
  ) { }

  ngOnInit(): void {
    this.getEmailTemplateList();
  }

  getEmailTemplateList() {
    GeneralFunction.setAuthorizeModule('template-management', 'list');
    this.spinner.show();
    const postData = { 'page': this.current_page, 'name': this.name, 'template_type': this.template_type, 'status': this.status };
    this.appSettingsService.getEmailTemplateList(postData).subscribe((res) => {
      // if(response.success==true){
      //   this.finalResponse = response.data.response;
      //   this.total = response.data.pagination.total;
      //   this.per_page = response.data.pagination.per_page;
      // }else{
      //   this.finalResponse = [];
      //   this.total =0;
      //   this.per_page = 0;
      // }

      // this.spinner.hide();
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
    // this.appSettingsService.getEmailTemplate().subscribe((data) => {
    //   this.finalResponse = data;
    //   this.spinner.hide();
    // });
  }
  // pageChangeEvent(event: number){
  //   this.page = event;
  //   this.getEmailTemplateList();
  // }
  setCurrent(pageNumber: number) {
    if (pageNumber > 0 && $.inArray(pageNumber, this.pager.pages) !== -1) {
      this.current_page = pageNumber;
      this.offset = (pageNumber - 1) * this.limit;
      this.start_record = this.offset + 1;
      this.getEmailTemplateList();
    }
  }
  deleteEmailTemplate(id: string) {
    let deleteArray = { title: 'Are you sure?', text: 'Do you want to delete this template?' };
    this.generalService.openDeleteConfirmationDialog(deleteArray).then(response => {
      if (response) {
        this.spinner.show();
        this.appSettingsService.deleteEmailTemplate(id).subscribe((res: any) => {
          if (res) {
            this.spinner.hide();
            if (res.success == true) {
              this.toastr.success(res.message, "Success");
              this.getEmailTemplateList();
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
        this.spinner.hide();
      }
    },
      (error) => {
        this.spinner.hide();
        this.generalService.getErrorMsg(error.error.message);
      });
  }
  searchData() {
    this.current_page = 1;
    // this.name = (<HTMLInputElement>document.getElementById('name')).value;
    // this.template_type = (<HTMLInputElement>document.getElementById('template_type')).value;
    // this.status = (<HTMLInputElement>document.getElementById('status')).value;
    this.getEmailTemplateList();
  }
  resetData() {
    this.name = '';
    this.template_type = '';
    this.status = '';
    this.getEmailTemplateList();
  }
}
