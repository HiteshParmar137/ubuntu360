import { Component, OnInit } from "@angular/core";
import { ToastrService } from "ngx-toastr";
import { NgxSpinnerService } from "ngx-spinner";
import { AppSettingsService } from "src/app/app-settings.service";
import { GeneralFunction } from "src/app/_directives/general-function.directive";
import { GeneralService } from "src/app/services/general.service";

@Component({
  selector: "app-menus",
  templateUrl: "./users.component.html",
  styleUrls: ["./users.component.scss"],
})
export class UsersComponent implements OnInit {
  responseFlag: boolean = false;
  noDataFlag: string = "";
  finalResponse: [] = [];
  search: string = "";
  sort: string = "";
  sort_dir: string = "";
  loginUserId = "";
  spinnerShow: string = "Yes";
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
    this.getUserList();
    this.loginUserId = localStorage.getItem("userId") || "";
  }

  getUserList() {
    GeneralFunction.setAuthorizeModule('users', 'list');
    if (this.search == ''){
      this.spinner.show();
    }
    const postData = { 'page': this.current_page, 'search': this.search ,'user_type':1};

    this.appSettingsService.getUsers(postData).subscribe((res) => {
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
      this.getUserList();
    }
  }

  deleteUser(id: string) {
    this.spinner.show();
    this.toastr.success(
      "User data deleted successfully",
      "Success"
    );
    this.spinner.hide();
  }

  reloadGetEmployer() {
    this.getUserList();
  }

  sortOn(column: string) {
    if (column) {
      this.sort = column;
      if (this.sort_dir == "") {
        this.sort_dir = "desc";
      } else {
        if (this.sort_dir === "asc") {
          this.sort_dir = "desc";
        } else {
          this.sort_dir = "asc";
        }
      }
    }
    this.getUserList();
  }

  resetFilter(){
    this.search = '';
    this.getUserList();
  }
}
