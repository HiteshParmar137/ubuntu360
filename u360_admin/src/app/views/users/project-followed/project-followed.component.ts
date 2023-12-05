import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import { GeneralFunction } from 'src/app/_directives/general-function.directive';
import { Constants } from 'src/app/services/constants';
import { GeneralService } from 'src/app/services/general.service';

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
  search: string = "";
  projectUsers: [] = [];
  user: string = "";
  categories: [] = [];
  category: string = "";
  status: {} = {};
  srchStatus: string = "";
  id: any;
  isCorporateUser: boolean = false;
  url: string = '';
  constructor(
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private appSettingsService: AppSettingsService,
    public router: Router,
    private route: ActivatedRoute,
    private generalService: GeneralService
  ) {
    this.route.paramMap.subscribe(params => {
      this.id = params.get('id');
    });
   }

  ngOnInit(): void {
    this.getProjectsList();
    this.getProjectUsersList();
    this.getCategoriesList();
    this.getStatusList();
    this.url = this.router.url;
    if (this.url.includes('user-management/corporate-users/')) {
      this.isCorporateUser = true;
    }
    console.log('this.isCorporateUser',this.isCorporateUser);  
  }

  getProjectsList() {
    GeneralFunction.setAuthorizeModule('projects', 'list');
    this.spinner.show();
    const postData = { 'page': this.current_page, 'title': this.search, 'user': this.id, 'category': this.category, 'status': this.srchStatus };
    this.appSettingsService.getFollowedProjectsList(postData).subscribe((res) => {
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
          console.log(this.finalResponse);
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

  getProjectUsersList() {
    this.appSettingsService.getProjectUsersList().subscribe((res: any) => {
      if (res) {
        if (res.success === true) {
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
        if (res.success === true) {
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

}
