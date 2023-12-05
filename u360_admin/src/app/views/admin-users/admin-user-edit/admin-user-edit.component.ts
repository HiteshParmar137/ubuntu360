import { Component, OnInit } from '@angular/core';
import { UntypedFormBuilder, UntypedFormGroup, Validators } from "@angular/forms";
import { ActivatedRoute, Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { NgxSpinnerService } from 'ngx-spinner';
import { Constants } from 'src/app/services/constants';
import { ApiOperationManagerService } from 'src/app/services/api/operation-manager/api/api-operation-manager.service';
import { userEditFilter } from 'src/app/data/filter/user/user-edit-filter';
import { GeneralFunction } from 'src/app/_directives/general-function.directive';
import { AppSettingsService } from 'src/app/app-settings.service';
import { GeneralService } from 'src/app/services/general.service';

@Component({
  selector: 'app-admin-user-edit',
  templateUrl: './admin-user-edit.component.html',
  styleUrls: ['./admin-user-edit.component.scss']
})
export class AdminUserEditComponent implements OnInit {

  id: any;
  groupList: [] = [];
  isSubmitted = false;
  current_page: any = 1;
  userEdit: UntypedFormGroup = new UntypedFormGroup({});

  constructor(
    private formBuilder: UntypedFormBuilder,
    private apiService: ApiOperationManagerService,
    private toastr: ToastrService,
    public router: Router,
    private route: ActivatedRoute,
    private appSettingsService: AppSettingsService,
    private spinner: NgxSpinnerService,
    private generalService: GeneralService
  ) {
    this.route.paramMap.subscribe(params => {
      this.id = params.get('id');
    });
  }

  ngOnInit(): void {
    if (this.id) {
      this.getAdminUserDetail();
    }
    this.generateFormObj();
    this.getUserGroups();
  }

  generateFormObj() {
    this.userEdit = this.formBuilder.group({
      id: ["", Validators.required],
      name: ["", Validators.required],
      email: ["", [Validators.required, Validators.email]],
      status: [""],
      user_group_id: ["", Validators.required],
    });
  }

  getUserGroups() {
    this.appSettingsService.getUserGroups().subscribe((res: any) => {
      if (res.success == true) {
        this.groupList = res.data;
      }
    });
  }

  getAdminUserDetail() {
    this.appSettingsService.getAdminUserDetail(this.id).subscribe((res) => {
      if (res.success == true) {
        let users = res.data;
        this.userEdit = this.formBuilder.group({
          id: [this.id, [Validators.required]],
          name: [users.name, [Validators.required]],
          email: [users.email, [Validators.required, Validators.email]],
          status: [users.status == '1' ? true : false],
          user_group_id: [users.user_group_id, [Validators.required]],
        });
      } else {
        this.toastr.error("Invalid User", "Error");
      }
      this.spinner.hide();
    });
  }

  get f() {
    return this.userEdit.controls;
  }

  onSubmit(): void {
    this.isSubmitted = true;

    if (this.userEdit.invalid) {
      return;
    }
    this.spinner.show();
    this.userEdit.value.status = this.userEdit.value.status == true ? "1" : "0",
      this.appSettingsService.adminUserUpdate(this.userEdit.value).subscribe((res: any) => {
        if (res) {
          this.spinner.hide();
          if (res.success == true) {
            this.toastr.success(res.message, "Success");
            this.router.navigate(['/admin-management/admin-users']);
          } else {
            this.generalService.getErrorMsg(res.message);
          }
        }
        this.spinner.hide();
      });

  }

}
