import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import { GeneralFunction } from 'src/app/_directives/general-function.directive';
import { Constants } from 'src/app/services/constants';
import { GeneralService } from 'src/app/services/general.service';
@Component({
  selector: 'app-project',
  templateUrl: './project.component.html',
  styleUrls: ['./project.component.scss']
})
export class ProjectComponent implements OnInit {

  finalResponse: [] = [];
  responseFlag: boolean = false;
  noDataFlag: string = "";
  page: number = 1;
  total: number = 0;
  per_page: number = 0;
  current_page: any = 1;
  limit: any;
  offset: any;
  perPageRecord = [];
  pager: any = {};
  index: number = 0;
  start_record: any;
  search: string = "";
  projectUsers: [] = [];
  user: string = "";
  categories: [] = [];
  category: string = "";
  status: {} = {};
  srchStatus: string = "";
  constructor(
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private appSettingsService: AppSettingsService,
    public router: Router,
    private generalService: GeneralService
  ) { }

  ngOnInit(): void {
    this.getProjectsList();
    this.getProjectUsersList();
    this.getCategoriesList();
    this.getStatusList();
  }

  getProjectsList(type='') {
    GeneralFunction.setAuthorizeModule('projects', 'list');
    if(type!='search'){
      this.spinner.show();
    }    
    const postData = { 'page': this.current_page, 'title': this.search, 'user': this.user, 'category': this.category, 'status': this.srchStatus };
    this.appSettingsService.getProjectsList(postData).subscribe((res) => {
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
      this.getProjectsList();
    }
  }

  deleteProject(id: any) {
    this.spinner.show();
    this.appSettingsService.deleteProject(id).subscribe((res: any) => {
      if (res) {
        this.spinner.hide();
        if (res.success == true) {
          this.toastr.success(res.message, "Success");
          this.getProjectsList();
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

  reloadProjectsList(type:any='') {
    this.current_page = 1;
    this.getProjectsList(type);
  }

  getProjectUsersList() {
    this.appSettingsService.getProjectUsersList().subscribe((res: any) => {
      if (res) {
        if (res.success == true) {
          this.projectUsers = res.data;
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

  getStatusList() {
    this.status = Constants.PROJECTSTATUSARR;
  }

  resetFilter() {
    this.search = "";
    this.user = "";
    this.category = "";
    this.srchStatus = "";
    this.getProjectsList();
  }
}
