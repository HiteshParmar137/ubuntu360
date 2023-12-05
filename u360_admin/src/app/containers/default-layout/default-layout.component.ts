import { AppSettingsService } from 'src/app/app-settings.service';
import { Component, OnInit } from '@angular/core';
import { NgxSpinnerService } from 'ngx-spinner'
import { ApiOperationManagerService } from 'src/app/services/api/operation-manager/api/api-operation-manager.service';
import { ToastrService } from 'ngx-toastr';
import { Constants } from 'src/app/services/constants';
import { GeneralService } from 'src/app/services/general.service';
@Component({
  selector: 'app-dashboard',
  templateUrl: './default-layout.component.html'
})
export class DefaultLayoutComponent implements OnInit {

  navItems: any;
  id: string | undefined;
  type!: string;
  userNameSubscriber: any;
  // public perfectScrollbarConfig = {
  //   suppressScrollX: true,
  // };
  userName: string = "";
  userProfile: string = "";
  groupId: string = "";
  adminModule:any=[];
  constructor(
    private spinner: NgxSpinnerService,
    private apiService: ApiOperationManagerService,
    private toastr: ToastrService,
    private appSettingsService: AppSettingsService,
    private GeneralService: GeneralService
  ) {
    this.userNameSubscriber =  this.GeneralService.apiDataListener.subscribe((data:any) => {
      this.userName = data.name;
      this.userProfile = data.profile_image;
      this.groupId = data.user_group_id;
    });
  }

  ngOnInit() {
    this.userName = localStorage.getItem('name') || '';
    this.userProfile = localStorage.getItem('image') || '';
    this.groupId = localStorage.getItem('user_group_id') || '';
    this.adminModule = localStorage.getItem('admin_module');
    this.navItems = this.adminModule ? JSON.parse(this.adminModule) : [];
    console.log(this.navItems);
  }

  // getSidebar() {
  //   this.spinner.show();
  //   if (this.groupId == "4") {
  //     this.appSettingsService.getsidebarTutorJSON().subscribe((data) => {
  //       this.navItems = data;
  //     });
  //   } else if (this.groupId == "5") {
  //     this.appSettingsService.getsidebarStaffJSON().subscribe((data) => {
  //       this.navItems = data;
  //     });
  //   } else if (this.groupId == "3") {
  //     this.appSettingsService.getsidebarDiplomaJSON().subscribe((data) => {
  //       this.navItems = data;
  //     });
  //   } else if (this.groupId == "6") {
  //     this.appSettingsService.getsidebarPupilsJSON().subscribe((data) => {
  //       this.navItems = data;
  //     });
  //   } else if (this.groupId == "7") {
  //     this.appSettingsService.getSidebarDiplomaViewerJSON().subscribe((data) => {
  //       this.navItems = data;
  //     });
  //   } else {
  //     this.appSettingsService.getSidebarJSON().subscribe((data) => {
  //       // let allData = data.filter((item: any) => {
  //       //   if (this.groupId == "4" && item.url == "tutors-dashboard") {
  //       //     return item;
  //       //   }
  //       //   if (this.groupId != "4" && item.url == "dashboard") {
  //       //     return item;
  //       //   }
  //       //   if (item.url != "tutors-dashboard" && item.url != "dashboard") {
  //       //     return item;
  //       //   }
  //       // });
  //       this.navItems = data;
  //     });
  //   }
  //   this.spinner.hide();


  //   // this.apiService
  //   //   .getSidebar()
  //   //   .then((res) => {
  //   //     if (res.data.success === true) {
  //   //       this.navItems = res.data.data;
  //   //     }
  //   //     this.spinner.hide();
  //   //   })
  //   //   .catch((err: any) => {
  //   //     this.spinner.hide();
  //   //     this.toastr.error(Constants.API_ERROR, 'Error');
  //   //   });
  // }
}
