import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import {UntypedFormBuilder, UntypedFormGroup } from '@angular/forms';
import { GeneralFunction } from "src/app/_directives/general-function.directive";
import { GeneralService } from 'src/app/services/general.service';
import { Constants } from 'src/app/services/constants';
import * as $ from 'jquery';
import 'bootstrap';
import 'bootstrap-datepicker';
import 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.css';
import 'node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'
@Component({
  selector: 'app-project-report',
  templateUrl: './project-report.component.html',
  styleUrls: ['./project-report.component.scss']
})
export class ProjectReportComponent implements OnInit {

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
  type: string = "";
  donation_type: string = "";
  category: string = "";
  status: string = "";
  created_at: string = "";
  created_at_dp:string = "";
  project_status:any = [];
  categories:any = [];
  isDispalayDatepicker:boolean = false;
  constructor(
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private appSettingsService: AppSettingsService,
    public router: Router,
    private formBuilder: UntypedFormBuilder,
    private generalService: GeneralService
  ) { }

  ngOnInit(): void {
    this.project_status = Constants.PROJECTSTATUSARR;
    this.getSubscription();
    this.getCategoriesList();
    $('.recurring_date').datepicker({
      format: 'dd/mm/yyyy',
      autoclose: true,
      // startDate: 'y',
      endDate: '+10y',
    }).on('changeDate', (e) => {
      // Get the selected date
      let selectedDate = e.format('yyyy-mm-dd');
      this.created_at_dp = e.format('dd/mm/yyyy');
      this.created_at = selectedDate;
      this.reloadSubscription();
      // Update the form control value with the selected date
      // this.recuringDonationForm.controls['recurring_date'].setValue(selectedDate);
    });
  }

  getCategoriesList() {
    this.appSettingsService.getCategoriesList().subscribe((res: any) => {
      if (res) {
        if (res.success == true) {
          this.categories = res.data;
        } else {
          this.toastr.error(res.message, "error");
        }
      }
    },
    (error) => {
      this.spinner.hide();        
      this.generalService.getErrorMsg(error.error.message);
    });
  }
  reloadSubscription(){
    this.getSubscription();
  }
  removeDateFilter(){
    $('.recurring_date').val('');
    this.created_at_dp = '';
    this.created_at = '';
    this.reloadSubscription();
  }
    resetFilter(){
      this.email = "";
      this.type = "";
      this.donation_type = "";
      this.category = "";
      this.status = "";
      this.created_at = "";
      this.getSubscription();
    }
  getSubscription() {
    // GeneralFunction.setAuthorizeModule('projects', 'list');
    if (this.search == '' ){
      this.spinner.show();
    }
    const postData = { 
      'page': this.current_page,
      'email': this.email,
      'type': this.type,
      'donation_type': this.donation_type,
      'category': this.category,
      'status': this.status,
      'created_at': this.created_at,
    };
    this.appSettingsService.getProjectReport(postData).subscribe((res) => {
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
              this.getSubscription();
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
      this.getSubscription();
    }
  }
  export(){
    this.spinner.show();
    this.appSettingsService.exportProjectReport(
      {
        type:'project',
        email:this.email,
        project_type:this.type,
        donation_type:this.donation_type,
        category:this.category,
        status:this.status,
        created_at:this.created_at,
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

