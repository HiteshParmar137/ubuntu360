import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import {UntypedFormBuilder, UntypedFormGroup } from '@angular/forms';
import { GeneralFunction } from "src/app/_directives/general-function.directive";
import { GeneralService } from 'src/app/services/general.service';
@Component({
  selector: 'app-user-report',
  templateUrl: './user-report.component.html',
  styleUrls: ['./user-report.component.scss']
})
export class UserReportComponent implements OnInit {
  isSubmitted = false;
  finalResponse: [] = [];
  spinnerShow: string = "Yes";
  page: number = 1;
  total: number = 0;
  per_page: number = 0;
  filterForm: UntypedFormGroup = new UntypedFormGroup({});
  name: string = '';
  current_page: any = 1;
  limit: any;
  offset: any;
  perPageRecord = [];
  pager: any = {};
  index: number = 0;
  start_record: any;
  responseFlag: boolean = false;
  noDataFlag: string = "";
  search: string = "";
  email: string = "";
  position: string = "";
  sponser: string = "";
  constructor(
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private appSettingsService: AppSettingsService,
    public router: Router,
    private formBuilder: UntypedFormBuilder,
    private generalService: GeneralService
  ) { }

  ngOnInit(): void {
    this.getUserReport();
  }

  reloadUserReport(){
    this.getUserReport();
  }

  resetFilter(){
    this.sponser = '';
    this.position = '';
    this.email = '';
    this.getUserReport();
  }

  getUserReport() {
    // GeneralFunction.setAuthorizeModule('projects', 'list');
    if (this.search == '' ){
      this.spinner.show();
    }
    const postData = { 
      'page': this.current_page,
      'email': this.email,
      'position': this.position,
      'sponserType': this.sponser
    };
    this.appSettingsService.getUserReport(postData).subscribe((res) => {
      if (res.success === true) {
        console.log(res);
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

  deleteSubscription(id:string) {
    let deleteArray = {title: 'Are you sure?',text:'Do you want to delete this email?'};
    this.generalService.openDeleteConfirmationDialog(deleteArray).then(response => {
      if(response){
        this.spinner.hide();
        this.appSettingsService.deleteSubscription(id).subscribe((res) => {
          if (res) {
            this.spinner.hide();
            if (res.success == true) {
              this.toastr.success(res.message, "Success");
              this.getUserReport();
              this.spinner.hide();
            } else {
              this.toastr.error(res.message, "error");
              this.spinner.hide();
            }
          }
          this.spinner.hide();
        })
        this.spinner.hide();
      }
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
      this.getUserReport();
    }
  }

  export(){
    this.spinner.show();
    this.appSettingsService.exportUserReport(
      {
        'type':'user',
        'page': this.current_page,
        'email': this.email,
        'position': this.position,
        'sponserType': this.sponser
      }
    ).subscribe((res) => {
      if (res.success === true) {
        this.toastr.success(res.message, "success");
      } else {       
        this.generalService.getErrorMsg(res.message);
      }
      this.spinner.hide();
    },
    (error) => {
      this.spinner.hide();
      this.generalService.getErrorMsg(error.error.message);
    });
  }
}
