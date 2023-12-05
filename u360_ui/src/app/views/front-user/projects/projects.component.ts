import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import { Constants } from 'src/app/services/constants';
import { GeneralFunction } from "src/app/_directives/general-function.directive";
import { GeneralService } from 'src/app/services/general.service';
@Component({
  selector: 'app-projects',
  templateUrl: './projects.component.html',
  styleUrls: ['./projects.component.scss']
})
export class ProjectsComponent implements OnInit {

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
  followObj: any = {};
  search: string = '';
  srchStatus: string = '';
  status: {} = {};
  last_page: string = '';
  constructor(
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private appSettingsService: AppSettingsService,
    public router: Router,
    public generalService: GeneralService,
  ) { }

  ngOnInit(): void {
    this.getProjectsList();
    this.getStatusList();
    localStorage.setItem("project_list_type", 'list');
  }

  getProjectsList() {
    this.spinner.show();
    GeneralFunction.setAuthorizeModule('cms-management', 'list');
    const postData = { 'page': this.current_page, 'title': this.search, 'status': this.srchStatus };
    this.appSettingsService.getUerProjectsList(postData).subscribe((res) => {
      if (res.success === true) {
        this.responseFlag = true;
        if (res.data && res.data.response && res.data.response.length > 0) {
          this.index = (this.current_page - 1) * res.data.pagination.per_page + 1;
          this.limit = res.data.pagination.per_page;
          this.last_page = res.data.pagination.last_page;
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
      this.generalService.getErrorMsg(error.error.message);
    });
    this.spinner.hide();
  }

  followUnfollow(id: any, type: any) {
    this.followObj = {
      'project_id': id,
      'type': type,
    };
    this.appSettingsService.followUnfollowProject(this.followObj).subscribe((res: any) => {
      if (res) {
        if (res.success == true) {
          this.toastr.success(res.message, "Success");
          this.getProjectsList();
        } else {
          this.toastr.error(res.message, "error");
        }
      }
    },
    (error) => {             
      this.generalService.getErrorMsg(error.error.message);
    });

  }

  resetFilter() {
    this.search = "";
    this.srchStatus = "";
    this.getProjectsList();
  }

  reloadProjectsList() {
    this.current_page = 1;
    this.getProjectsList();
  }

  getStatusList() {
    this.status = Constants.PROJECTSTATUSARR;
  }

  async completeProject(id: any) {
    let deleteArray = { 'title': 'Are you sure?', 'text': 'You want to complete this project?', 'data': id, 'api_name': 'close' };
    const confirmarion = await this.generalService.openDeleteConfirmationDialog(deleteArray);
    if (confirmarion) {
      this.spinner.show();
        this.appSettingsService.closeProject(id).subscribe((res: any) => {
          if (res) {
            if (res.success === true) {
              this.getProjectsList();
              this.spinner.hide();
              this.toastr.success(res.message, "Success");
            } else {
              this.spinner.hide();
              this.toastr.error(res.message, "error");
            }
          }
        },
        (error) => {             
          this.generalService.getErrorMsg(error.error.message);
        });
      }
  }

  searchTitle(event:any){
    this.search = event.target.value;   
    this.getProjectsList();
  }

}
