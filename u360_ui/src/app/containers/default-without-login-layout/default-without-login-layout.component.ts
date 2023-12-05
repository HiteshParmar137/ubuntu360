import { AppSettingsService } from 'src/app/app-settings.service';
import { Component, OnInit } from '@angular/core';
import { GeneralService } from '../../services/general.service';
import { Subscription } from 'rxjs';
import * as $ from 'jquery';
@Component({
  selector: 'app-without-login',
  templateUrl: './default-without-login-layout.component.html'
})
export class DefaultWithoutLoginLayoutComponent implements OnInit {

  navItems: any;
  id: string | undefined;
  type!: string;
  userNameSubscriber: Subscription;
  userName: string = "";
  userProfile: string = "";
  groupId: string = "";
  userId: string = "";
  userLoggedIn = false;
  siteSetting: any = [];
  constructor(
    private appSettingsService: AppSettingsService,
    private generalService: GeneralService,
  ) {
    this.userNameSubscriber = this.generalService.apiDataListener.subscribe((data: any) => {
      this.userName = data.name;
      this.userProfile = data.profile_image;
      this.groupId = data.user_group_id;
    });

  }

  ngOnInit() {
    this.userName = localStorage.getItem('name') ?? '';
    this.userProfile = localStorage.getItem('image') ?? '';
    this.groupId = localStorage.getItem('user_group_id') ?? '';
    this.userId = localStorage.getItem('userId') ?? '';
    this.appSettingsService.getSiteSetting().subscribe((res) => {
      if (res.success === true) {
        for (let setting of res.data.siteSettings) {
          this.siteSetting[setting.key] = setting.value;
        }
        this.generalService.siteSettingData(this.siteSetting);
      }
    });

    /*===== select-color =======*/

    $('select.form-control').css('color', '#BDBDBD');
    $('select.form-control').change(function () {
      let current = $('select.form-control').val();
      if (current != 'null') {
        $(this).css('color', '#363853');
      } else {
        $(this).css('color', '#BDBDBD');
      }
    });
  }
}
