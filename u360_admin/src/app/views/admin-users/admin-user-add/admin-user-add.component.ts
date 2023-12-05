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
  selector: 'app-admin-user-add',
  templateUrl: './admin-user-add.component.html',
  styleUrls: ['./admin-user-add.component.scss']
})
export class AdminUserAddComponent implements OnInit {
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

  }

  ngOnInit(): void {
    this.generateFormObj();
    this.getUserGroups();
  }

  getUserGroups() {
    this.appSettingsService.getUserGroups().subscribe((res: any) => {
      if (res.success == true) {
        this.groupList = res.data;
      }
    });
  }

  generateFormObj() {
    this.userEdit = this.formBuilder.group({
      name: ["", Validators.required],
      email: ["", [Validators.required, Validators.email]],
      status: [""],
      user_group_id: ["", Validators.required],
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
      this.appSettingsService.adminUserAdd(this.userEdit.value).subscribe((res: any) => {
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
      },
      (error) => {     
        this.spinner.hide();        
        this.generalService.getErrorMsg(error.error.message);
      });

  }

}
