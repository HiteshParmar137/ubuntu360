import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import { AbstractControl, UntypedFormBuilder, UntypedFormGroup, Validators } from '@angular/forms';
import { GeneralFunction } from "src/app/_directives/general-function.directive";
import { GeneralService } from 'src/app/services/general.service';

@Component({
  selector: 'app-feedback',
  templateUrl: './feedback.component.html',
  styleUrls: ['./feedback.component.scss']
})
export class FeedbackComponent implements OnInit {

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

  constructor(
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private appSettingsService: AppSettingsService,
    public router: Router,
    private formBuilder: UntypedFormBuilder,
    private generalService: GeneralService
  ) { }

  ngOnInit(): void {
    this.getFeedbackList();
  }

  getFeedbackList() {
    GeneralFunction.setAuthorizeModule('feedback', 'list');
    this.spinner.show();
    const postData = { 'page': this.current_page, 'name': this.name };
    this.appSettingsService.getFeedbackList(postData).subscribe((res) => {
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
  
  deleteFeedback(id: string) {
    this.spinner.show();
    this.appSettingsService.deleteFeedback(id).subscribe((res: any) => {
      if (res) {
        this.spinner.hide();
        if (res.success == true) {
          this.toastr.success(res.message, "Success");
          this.getFeedbackList();
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
  setCurrent(pageNumber: number) {
    if (pageNumber > 0 && $.inArray(pageNumber, this.pager.pages) !== -1) {
      this.current_page = pageNumber;
      this.offset = (pageNumber - 1) * this.limit;
      this.start_record = this.offset + 1;
      this.getFeedbackList();
    }
  }
  searchData() {
    this.current_page = 1;
    // this.name = (<HTMLInputElement>document.getElementById('name')).value;
    // this.template_type = (<HTMLInputElement>document.getElementById('template_type')).value;
    // this.status = (<HTMLInputElement>document.getElementById('status')).value;
    this.getFeedbackList();
  }
  resetData() {
    this.name = '';
    this.getFeedbackList();
  }

}
