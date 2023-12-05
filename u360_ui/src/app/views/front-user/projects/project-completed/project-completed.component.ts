import { AppSettingsService } from 'src/app/app-settings.service';
import { Component, OnInit } from '@angular/core';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { GeneralService } from 'src/app/services/general.service';
import { GeneralFunction } from "src/app/_directives/general-function.directive";
import { ActivatedRoute } from '@angular/router';
import { AuthService } from 'src/app/views/front-user/auth/auth.service';
@Component({
  selector: 'app-project-completed',
  templateUrl: './project-completed.component.html',
  styleUrls: ['./project-completed.component.scss']
})
export class ProjectCompletedComponent implements OnInit {

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
    public authService: AuthService,
    private route: ActivatedRoute
  ) { }

  ngOnInit(): void {
    this.currentUrl = this.route.snapshot.url.join('/');
    localStorage.setItem("currentUrl", this.currentUrl);
    this.getFollowedProjectsList();
    localStorage.setItem("project_list_type", 'complete');
  }

  getFollowedProjectsList() {
    this.spinner.show();
    GeneralFunction.setAuthorizeModule('cms-management', 'list');
    const statuses = ['Completed by Owner', 'Goal Reached'];
    const postData = { 'page': this.current_page, 'title': this.search, 'status': statuses };
    this.appSettingsService.getUserCompletedProjectsList(postData).subscribe({
      next:(res) => {
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
      },
      error:(error) => {             
        this.generalService.getErrorMsg(error.error.message);
      }
    });
  }

  setCurrent(pageNumber: number) {
    if (pageNumber > 0 && $.inArray(pageNumber, this.pager.pages) !== -1) {
      this.current_page = pageNumber;
      this.offset = (pageNumber - 1) * this.limit;
      this.start_record = this.offset + 1;
      this.getFollowedProjectsList();
    }
  }

  searchTitle(event:any){
    this.search = event.target.value;
    this.getFollowedProjectsList();
  }
}
