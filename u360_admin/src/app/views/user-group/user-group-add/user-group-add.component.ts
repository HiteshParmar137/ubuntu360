import { AppSettingsService } from 'src/app/app-settings.service';
import { Component, OnInit } from '@angular/core';
import {
  AbstractControl,
  FormBuilder,
  FormGroup,
  Validators,
} from '@angular/forms';
import { ToastrService } from 'ngx-toastr';
import { NgxSpinnerService } from 'ngx-spinner';
import { ApiOperationManagerService } from 'src/app/services/api/operation-manager/api/api-operation-manager.service';
import * as $ from 'jquery';
import { groupAddFilter } from 'src/app/data/filter/user-group/group-add-filter';
import { Router } from '@angular/router';
import { GeneralService } from 'src/app/services/general.service';
import { Constants } from 'src/app/services/constants';
import { GeneralFunction } from 'src/app/_directives/general-function.directive';

@Component({
  selector: 'app-user-group-add',
  templateUrl: './user-group-add.component.html',
  styleUrls: ['./user-group-add.component.scss']
})
export class UserGroupAddComponent implements OnInit {
  groupAddForm: FormGroup = new FormGroup({});
  submitted = false;
  responseFlag: boolean = false;
  finalResponse: any[] = [];
  noDataFlag: string = '';

  constructor(
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private formBuilder: FormBuilder,
    public router: Router,
    public generalService: GeneralService,
    private apiService: ApiOperationManagerService,
    private appSettingsService: AppSettingsService,
    private generalSerice: GeneralService
  ) { }

  ngOnInit(): void {
    GeneralFunction.setAuthorizeModule('user-group', 'add');
    this.getSystemModule();
    this.generateFormObj();
  }

  getSystemModule() {
    this.spinner.show();
    this.apiService
      .getSystemModule()
      .then((res) => {
        if (res.data.success === true) {
          this.responseFlag = true;
          this.finalResponse = res.data.data.response;
          console.log(res.data.data.response);
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

  generateFormObj() {
    this.groupAddForm = this.formBuilder.group({
      group_name: ['', Validators.required],
      description: [''],
      year_group: [''],
      status: ['']
    });
  }

  get f(): { [key: string]: AbstractControl } {
    return this.groupAddForm.controls;
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
    GeneralFunction.setAuthorizeModule('user-group', 'add');
    this.submitted = true;
    if (this.groupAddForm.invalid) {
      return;
    }
    let permission: any = {};
    $(".checkbox-group:checkbox:checked").map(function () {
      let name: any = $(this).attr("name");
      let value: any = $(this).val();
      permission[name] = permission.hasOwnProperty(name)
        ? [...permission[name], value]
        : [value]
      return permission;
    }).get();
    const requestObj: groupAddFilter = new groupAddFilter(
      this.groupAddForm.value.group_name.trim(),
      this.groupAddForm.value.description.trim(),
      this.groupAddForm.value.year_group,
      this.groupAddForm.value.status = this.groupAddForm.value.status == true ? "1" : "0",
      JSON.stringify(permission)
    );
    this.apiService
      .userGroupCreate(requestObj)
      .then((res) => {
        if (res.data.success === true) {
          this.toastr.success("Users Group added successfully", 'Success');
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
    this.generalSerice.aclChecker(path, module, action);
    return false;
  }
}
