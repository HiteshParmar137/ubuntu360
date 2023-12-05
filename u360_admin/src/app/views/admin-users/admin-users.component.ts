import { Component, OnInit } from "@angular/core";
import { ToastrService } from "ngx-toastr";
import { NgxSpinnerService } from "ngx-spinner";
import { AppSettingsService } from "src/app/app-settings.service";
import { GeneralFunction } from "src/app/_directives/general-function.directive";
import { GeneralService } from "src/app/services/general.service";

@Component({
  selector: 'app-admin-users',
  templateUrl: './admin-users.component.html',
  styleUrls: ['./admin-users.component.scss']
})
export class AdminUsersComponent implements OnInit {

  responseFlag: boolean = false;
  noDataFlag: string = "";
  finalResponse: [] = [];
  search: string = "";
  sort: string = "";
  sort_dir: string = "";
  spinnerShow: string = "Yes";
  POSTS: any;
  page: number = 1;
  total: number = 0;
  count: number = 0;
  per_page: number = 0;
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
    private generalService: GeneralService
  ) { }

  ngOnInit(): void {
    this.getAdminUserList();
  }

  getAdminUserList() {
    GeneralFunction.setAuthorizeModule('admin-users', 'list');
    this.spinner.show();
    const postData = { 'page': this.current_page, search: this.search }
    this.appSettingsService.getAdminUser(postData).subscribe((res) => {
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
    });
  }

  setCurrent(pageNumber: number) {
    if (pageNumber > 0 && $.inArray(pageNumber, this.pager.pages) !== -1) {
      this.current_page = pageNumber;
      this.offset = (pageNumber - 1) * this.limit;
      this.start_record = this.offset + 1;
      this.getAdminUserList();
    }
  }

  // pageChangeEvent(event: number){
  //   this.page = event;
  //   this.getAdminUserList();
  // }

  deleteUser(id: string) {
    this.spinner.show();
    this.appSettingsService.deleteAdminUser(id).subscribe((res: any) => {
      if (res) {
        this.spinner.hide();
        if (res.success == true) {
          this.toastr.success(res.message, "Success");
          this.getAdminUserList();
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

  reloadGetEmployer() {
    this.current_page = 1;
    this.getAdminUserList();
  }

}
