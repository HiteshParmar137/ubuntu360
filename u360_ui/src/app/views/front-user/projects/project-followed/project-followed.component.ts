import { Component, OnInit } from '@angular/core';
import { NgxSpinnerService } from 'ngx-spinner';
import { GeneralService } from 'src/app/services/general.service';
import { GeneralFunction } from "src/app/_directives/general-function.directive";
import { AppSettingsService } from 'src/app/app-settings.service';
import { Constants } from 'src/app/services/constants';
import { ToastrService } from 'ngx-toastr';
import { ActivatedRoute } from '@angular/router';
@Component({
  selector: 'app-project-followed',
  templateUrl: './project-followed.component.html',
  styleUrls: ['./project-followed.component.scss']
})
export class ProjectFollowedComponent implements OnInit {

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
  currentUrl:string = '';
  constructor(
    private toaster: ToastrService,
    private spinner: NgxSpinnerService,
    public generalService: GeneralService,
    private appSettingsService: AppSettingsService,
    private route: ActivatedRoute
  ) { }

  ngOnInit(): void {
    this.getFollowedProjectsList();
    this.currentUrl = this.route.snapshot.url.join('/');
    localStorage.setItem("project_list_type", 'follow');
  }

  getFollowedProjectsList() {
    this.spinner.show();
    GeneralFunction.setAuthorizeModule('cms-management', 'list');
    const postData = { 'page': this.current_page, 'title': this.search, 'status': this.srchStatus };
    this.appSettingsService.getUserFollowedProjectsList(postData).subscribe((res) => {
      if (res.success === true) {
        this.responseFlag = true;
        if (res.data?.response?.length > 0) {
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
        if (this.current_page > 1) {
          this.setCurrent(this.current_page - 1);
        }
        this.finalResponse = [];
        this.noDataFlag = "Data Not Found";
      }
      this.spinner.hide();
    });
  }

  reloadProjectsList() {
    this.current_page = 1;
    this.getFollowedProjectsList();
  }

  resetFilter() {
    this.search = "";
    this.srchStatus = "";
    this.getFollowedProjectsList();
  }

  getStatusList() {
    this.status = Constants.PROJECTSTATUSARR;
  }
  setCurrent(pageNumber: number) {
    if (pageNumber > 0 && $.inArray(pageNumber, this.pager.pages) !== -1) {
      this.current_page = pageNumber;
      this.offset = (pageNumber - 1) * this.limit;
      this.start_record = this.offset + 1;
      this.getFollowedProjectsList();
    }
  }

  followUnfollow(id: any, type: any) {
    this.followObj = {
      'project_id': id,
      'type': type,
    };
    let deleteArray = { 'title': 'Are you sure?', 'text': 'You want to '+type+' this project?', 'data': id, 'api_name': 'follow/create' };
    this.generalService.openDeleteConfirmationDialog(deleteArray).then(response => {
      if (response) {
        this.spinner.show();
        this.appSettingsService.followUnfollowProject(this.followObj).subscribe({
          next:(res: any) => {
            if (res) {
              if (res.success) {
                this.toaster.success(res.message, "Success");
                this.getFollowedProjectsList();
              } else {
                this.toaster.error(res.message, "error");
              }
            }
          },
          error:(error) => {             
            this.generalService.getErrorMsg(error.error.message);
          }
        });
        this.spinner.hide();
      }
    });
  }

  searchTitle(event:any){
    this.search = event.target.value;
    this.getFollowedProjectsList();
  }
}
