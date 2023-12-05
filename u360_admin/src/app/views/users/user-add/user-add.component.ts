import { Component, OnInit } from "@angular/core";
import { UntypedFormBuilder, UntypedFormGroup, Validators } from "@angular/forms";
import { Router } from "@angular/router";
import { ToastrService } from "ngx-toastr";
import { NgxSpinnerService } from "ngx-spinner";
import { userCreateFilter } from "src/app/data/filter/user/user-create-filter";
import { AppSettingsService } from "src/app/app-settings.service";
import { GeneralService } from "src/app/services/general.service";

@Component({
  selector: "app-menu-add",
  templateUrl: "./user-add.component.html",
  styleUrls: ["./user-add.component.scss"],
})
export class UserAddComponent implements OnInit {
  groupList: [] = [];
  isSubmitted = false;
  current_page: any = 1;
  userAdd: UntypedFormGroup = new UntypedFormGroup({});

  constructor(
    private formBuilder: UntypedFormBuilder,
    private toastr: ToastrService,
    public router: Router,
    private spinner: NgxSpinnerService,
    private appSettingsService: AppSettingsService,
    private generalService: GeneralService
  ) {}

  ngOnInit(): void {
    this.getUserGroups();
    this.generateFormObj();
  }

  generateFormObj() {
    this.userAdd = this.formBuilder.group({
      name: ["", Validators.required],
      email: ["", [Validators.required, Validators.email]],
      password: ["", Validators.required],
      user_group_id: ["", Validators.required],
      status: [""],
    });
  }

  getUserGroups() {
    this.appSettingsService.getUserGroupJSON().subscribe((data) => {
      this.groupList = data;
      this.spinner.hide();
    },
    (error) => {     
      this.spinner.hide();        
      this.generalService.getErrorMsg(error.error.message);
    });
  }

  get f() {
    return this.userAdd.controls;
  }

  onSubmit(): void {
    this.isSubmitted = true;

    if (this.userAdd.invalid) {
      return;
    }
    this.spinner.show();

    const requestObj: userCreateFilter = new userCreateFilter(
      this.userAdd.value.name,
      this.userAdd.value.email,
      this.userAdd.value.status == true ? "on" : "off",
      this.userAdd.value.password,
      this.userAdd.value.user_group_id
    );

    this.toastr.success("User added successfully", "Success");
    this.spinner.hide();
    this.router.navigate(["/user-management/users"]);
  }
}
