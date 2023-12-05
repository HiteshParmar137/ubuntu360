import { AppSettingsService } from "src/app/app-settings.service";
import { Component, OnInit } from "@angular/core";
import { ToastrService } from "ngx-toastr";
import { NgxSpinnerService } from "ngx-spinner";
import { Constants } from "src/app/services/constants";
import { GeneralFunction } from "src/app/_directives/general-function.directive";
import { groupListFilter } from "src/app/data/filter/user-group/group-list-filter";
import { ApiOperationManagerService } from "src/app/services/api/operation-manager/api/api-operation-manager.service";
import { GeneralService } from "src/app/services/general.service";
import { ConfirmationDialogService } from "../confirmation-dialog/confirmation-dialog.service";
import Swal from 'sweetalert2';
@Component({
  selector: "app-user-group",
  templateUrl: "./user-group.component.html",
  styleUrls: ["./user-group.component.scss"],
})
export class UserGroupComponent implements OnInit {
  totalRecords: any = 0;
  offset: any;
  limit: any;
  perPageRecord = [];
  pager: any = {};
  current_page: any = 1;
  start_record: any;
  end_record: any;
  perPage: any;
  responseFlag: boolean = false;
  noDataFlag: string = "";
  userGroupList: [] = [];
  sort: string = "";
  sort_dir: string = "";
  filter_name: string = "";
  index: number = 0;
  last_page: string = '';
  status: string = '';
  constructor(
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private appSettingsService: AppSettingsService,
    private apiService: ApiOperationManagerService,
    private generalService: GeneralService,
    private confirmationDialogService: ConfirmationDialogService
  ) {}

  ngOnInit(): void {
    this.getSystemModule();
    this.getUserGroup();
  }
  
  getSystemModule(){
    this.spinner.show();
    this.apiService
      .getSystemModule()
      .then((res) => {
        if (res.data.success === true) {
          this.responseFlag = true;
          // this.finalResponse = res.data.data;
        } else {
          this.noDataFlag = "Data Not Found";
          // this.finalResponse = [];
        }
        this.spinner.hide();
      })
      .catch((err: any) => {
        this.spinner.hide();
        this.toastr.error(Constants.API_ERROR, 'Error');
      });
  }
  resetFilter(){
    this.filter_name = '';
    this.status = '';
    this.getUserGroup();
  }
  getUserGroup() {
    GeneralFunction.setAuthorizeModule('user-group', 'list');
    if (this.filter_name == ''){
      this.spinner.show();
    }
    const getObj: groupListFilter = new groupListFilter(
      this.current_page,
      this.sort,
      this.sort_dir,
      this.filter_name,
      this.status
    );

    this.apiService
      .getUserGroupList(getObj)
      .then((res) => {
        if (res.data.success === true) {
          this.responseFlag = true;
          if (res.data.data && res.data.data.response && res.data.data.response.length > 0) {
            this.index = (this.current_page - 1) * res.data.data.pagination.per_page + 1;
            this.limit = res.data.data.pagination.per_page;
            this.last_page = res.data.data.pagination.last_page;
            this.userGroupList = [];
            let currentPaginationObject = {
              range: this.current_page,
              total: res.data.data.pagination.total,
            };
            this.pager = GeneralFunction.setPagination(
              currentPaginationObject,
              this.current_page,
              this.limit
            );
            this.userGroupList = res.data.data.response;
          }else{
            this.userGroupList = [];
            this.noDataFlag = "Data Not Found";
          }
        } else {
          this.userGroupList = [];
          this.noDataFlag = "Data Not Found";
        }
        this.spinner.hide();
      })
      .catch((err: any) => {
        console.log(err,"Error log");
        this.spinner.hide();
        this.toastr.error(Constants.API_ERROR, 'Error');
      });
  }

  setCurrent(pageNumber: number) {
    if (pageNumber > 0 && $.inArray(pageNumber, this.pager.pages) !== -1) {
      this.current_page = pageNumber;
      this.offset = (pageNumber - 1) * this.limit;
      this.start_record = this.offset + 1;
      this.getUserGroup();
    }
  }

  deleteUserGroup(id: string) {
    
  }

  public openConfirmationDialog(id:string) {
    Swal.fire({
      title: 'Are you sure?',
      text: 'You want remove this post?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, go ahead.',
      cancelButtonText: 'No, let me think',
    }).then((result) => {
      if (result.value) {
        GeneralFunction.setAuthorizeModule('user-group', 'delete');
        this.spinner.show();
        this.apiService
          .deleteUserGroup(id)
          .then((res) => {
            if (res.data.success === true) {
              this.toastr.success("Users Group deleted successfully.", "Success");
              this.getUserGroup();  
            } else {
              this.noDataFlag = "Data Not Found";
            }
            this.spinner.hide();
          })
          .catch((err: any) => {
            this.spinner.hide();
            this.toastr.error(Constants.API_ERROR, 'Error');
          });
        // this.postService.delete(id).subscribe(res => {
        //   this.posts = this.posts.filter(item => item.id !== id);
        // });
        //Swal.fire('Removed!', 'Post deleted successfully!', 'success');
        // Swal.fire('Removed!', 'Post deleted successfully!', 'success').then(function() {
          
        // });
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        //Swal.fire('Cancelled', 'Post still in our database.)', 'error');
      }
    });
    // GeneralFunction.setAuthorizeModule('user-group', 'delete');
    // // this.confirmationDialogService.confirm('Please confirm', 'Are you sure want to delete the user group?')
    // // .then((confirmed:boolean) => {
    // //   if(confirmed){
    // let deleteArray = {title: 'Are you sure?',text:'Do you want to delete this email?'};
    // this.generalService.openDeleteConfirmationDialog(deleteArray).then(response => {
    //   if(response){
    //     this.deleteUserGroup(id);
    //   }    
    // });
  }

  sortOn(column: string) {
    if (column) {
      this.sort = column;
      if (this.sort_dir == "") {
        this.sort_dir = "asc";
      } else {
        if (this.sort_dir === "asc") {
          this.sort_dir = "desc";
        } else {
          this.sort_dir = "asc";
        }
      }
    }
    this.getUserGroup();
  }
  checkAuth(path: string, module: string, action: string) {
    this.generalService.aclChecker(path, module, action);
    return false;
  }
}
