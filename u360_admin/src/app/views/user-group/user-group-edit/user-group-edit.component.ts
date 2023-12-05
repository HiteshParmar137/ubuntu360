import { Component, OnInit } from "@angular/core";
import {
  AbstractControl,
  FormBuilder,
  FormGroup,
  Validators,
} from "@angular/forms";
import { ToastrService } from "ngx-toastr";
import { NgxSpinnerService } from "ngx-spinner";
import { ApiOperationManagerService } from "src/app/services/api/operation-manager/api/api-operation-manager.service";
import * as $ from "jquery";
import { groupEditFilter } from "src/app/data/filter/user-group/group-edit-filter";
import { ActivatedRoute, Router } from "@angular/router";
import { GeneralService } from "src/app/services/general.service";
import { AppSettingsService } from "src/app/app-settings.service";
import { Constants } from "src/app/services/constants";
import { GeneralFunction } from "src/app/_directives/general-function.directive";

@Component({
  selector: "app-user-group-edit",
  templateUrl: "./user-group-edit.component.html",
  styleUrls: ["./user-group-edit.component.scss"],
})
export class UserGroupEditComponent implements OnInit {
  groupEditForm: FormGroup = new FormGroup({});
  submitted = false;
  responseFlag: boolean = false;
  noDataFlag: string = "";
  year_group: any;
  finalResponse: any[] = [];
  id: any;
  mapToSearch = {};
  isSuperUser: boolean = false;
  permission: any = {};

  constructor(
    private apiService: ApiOperationManagerService,
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private formBuilder: FormBuilder,
    private route: ActivatedRoute,
    public router: Router,
    public generalService: GeneralService,
    private appSettingsService: AppSettingsService,
  ) {
    this.route.paramMap.subscribe((params) => {
      this.id = params.get("id");
    });
  }

  ngOnInit(): void {
    GeneralFunction.setAuthorizeModule('user-group', 'edit');
    this.getSystemModule();
    this.getUserGroupDetail();
    this.generateFormObj();
    if (this.id == 1) {
      this.isSuperUser = true;
    }
  }

  getSystemModule() {
    this.spinner.show();
    this.apiService
      .getSystemModule()
      .then((res) => {
        if (res.data.success === true) {
          this.responseFlag = true;
          this.finalResponse = res.data.data.response;
        } else {
          this.noDataFlag = "Data Not Found";
          this.finalResponse = [];
        }
        this.spinner.hide();
      })
      .catch((err: any) => {
        this.spinner.hide();
        this.toastr.error(Constants.API_ERROR, 'Error');
      });
  }

  getUserGroupDetail() {
    GeneralFunction.setAuthorizeModule('user-group', 'edit');
    this.spinner.show();
    this.apiService
      .userGroupDetail(this.id)
      .then((res) => {
        if (res.data.success === true) {
          this.year_group = res.data.data.year_group;
          if (res.data.data.permission) {
            this.mapToSearch = res.data.data.permission;
          }
          this.groupEditForm = this.formBuilder.group({
            group_name: [
              res.data.data.group_name,
              [Validators.required],
            ],
            description: [
              res.data.data.description,
            ],
            year_group: [res.data.data.year_group],
            status: [
              res.data.data.status == true ? 1 : 0,
            ],
          });
        }
        this.spinner.hide();
      })
      .catch((err: any) => {
        this.spinner.hide();
        this.toastr.error(Constants.API_ERROR, 'Error');
      });
  }

  generateFormObj() {
    this.groupEditForm = this.formBuilder.group({
      group_name: ["", Validators.required],
      description: [""],
      year_group: [""],
      status: [""],
    });
  }

  get f(): { [key: string]: AbstractControl } {
    return this.groupEditForm.controls;
  }
  listSelect(i: number, action: string) {
    if (action != 'list') {
      $(".oneChecked_list_" + i).prop("checked", true);
    } else if (action == 'list' && !$(".oneChecked_list_" + i).is(':checked')) {
      $(".oneChecked_add_" + i).prop("checked", false);
      $(".oneChecked_edit_" + i).prop("checked", false);
      $(".oneChecked_delete_" + i).prop("checked", false);
    }
  }
  onSubmit(): void {
    this.submitted = true;
    if (this.groupEditForm.invalid) {
      return;
    }

    let permission: any = {};
    $(".checkbox-group:checkbox:checked")
      .map(function () {
        let name: any = $(this).attr("name");
        let value: any = $(this).val();
        permission[name] = permission.hasOwnProperty(name)
          ? [...permission[name], value]
          : [value];
        return permission;
      })
      .get();

    const requestObj: groupEditFilter = new groupEditFilter(
      this.id,
      this.groupEditForm.value.group_name.trim(),
      this.groupEditForm.value.description.trim(),
      this.groupEditForm.value.status == true ? "1" : "0",
      this.groupEditForm.value.year_group,
      JSON.stringify(permission)
    );

    this.apiService
      .userGroupUpdate(requestObj)
      .then((res) => {
        if (res.data.success === true) {
          this.toastr.success("User Group updated successfully.", 'Success');
          this.router.navigate(['admin-management/user-groups']);
        } else {
          this.generalService.getErrorMsg(res.message);
        }
      })
      .catch((err: any) => {
        this.toastr.error(Constants.API_ERROR, 'Error');
      });
  }
  checkAuth(path: string, module: string, action: string) {
    this.generalService.aclChecker(path, module, action);
    return false;
  }
}
