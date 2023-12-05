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
  selector: 'app-menu-edit',
  templateUrl: './user-edit.component.html',
  styleUrls: ['./user-edit.component.scss']
})
export class UserEditComponent implements OnInit {
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
    private generalService: GeneralService,
    private spinner: NgxSpinnerService) { 
      this.route.paramMap.subscribe(params => {
        this.id = params.get('id');
      });
    }

    ngOnInit(): void {
      this.getUserDetail();
      this.getUserGroups();
      this.generateFormObj();
    }
  
    generateFormObj() {
      this.userEdit = this.formBuilder.group({
        name: ["", Validators.required],
        email: ["", [Validators.required, Validators.email]],
        password: [""],
        user_group_id: ["", Validators.required],
        status: [""],
      });
    }
  
    getUserDetail() {
      this.appSettingsService.getJSON().subscribe((data) => {
        let users = data.find((user: { id: any }) => user.id == this.id);
        if (users) {
          this.userEdit = this.formBuilder.group({
            name: [users.name, [Validators.required]],
            email: [users.email, [Validators.required, Validators.email]],
            user_group_id: [users.user_group_id, Validators.required],
            password: [""],
            status: [users.status === "Active" ? 1 : 0],
          });
        } else {
          this.toastr.error("Invalid User", "Error");
        }
        this.spinner.hide();
      },
      (error) => {     
        this.spinner.hide();        
        this.generalService.getErrorMsg(error.error.message);
      });
    }
  
    getUserGroups() {
      this.appSettingsService.getUserGroupJSON().subscribe((data) => {
        this.groupList = data;
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
  
      const requestObj: userEditFilter = new userEditFilter(
        this.id,
        this.userEdit.value.name,
        this.userEdit.value.email,
        this.userEdit.value.status == true ? "on" : "off",
        this.userEdit.value.password,
        this.userEdit.value.user_group_id
      );

      this.toastr.success('Users data updated successfully', 'Success');
      this.spinner.hide();
      this.router.navigate(['/user-management/users']);
    }
}
