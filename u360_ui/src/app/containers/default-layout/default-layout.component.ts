import { Component, OnInit } from '@angular/core';
import { GeneralService } from '../../services/general.service';
import { Subscription } from 'rxjs';
import { AuthService } from 'src/app/views/front-user/auth/auth.service';
import * as $ from 'jquery';
import { Router } from '@angular/router';
@Component({
  selector: 'app-dashboard',
  templateUrl: './default-layout.component.html'
})
export class DefaultLayoutComponent implements OnInit {

  navItems: any;
  id: string | undefined;
  type!: string;
  userNameSubscriber: Subscription;
  userName: string = "";
  userProfile: string = "";
  groupId: string = "";
  userType: string = "";
  currentUrl: string = '';
  constructor(
    private generalService: GeneralService,
    public authService: AuthService,
    private router: Router
  ) {
    this.currentUrl = this.router.url;
    this.userNameSubscriber = this.generalService.apiDataListener.subscribe((data: any) => {
      this.userName = data.name;
      this.userProfile = data.profile_image;
      this.groupId = data.user_group_id;
    },
      (error) => {
        this.generalService.getErrorMsg(error.error.message);
      });
  }

  ngOnInit() {
    this.userName = localStorage.getItem('name') ?? '';
    this.userProfile = localStorage.getItem('image') ?? '';
    this.groupId = localStorage.getItem('user_group_id') ?? '';
    this.userType = localStorage.getItem('user_type') ?? '';

    /*===== dashboard-menu-open =======*/
    $('.ds-mobile-icon').click(function () {
      $("body").toggleClass("dashboard-open");
    });
    $(document).on('click', function () {
      $("body").removeClass("dashboard-open");
    });
    $(".dashboard-menu-top ul li a").on('click', function () {
      $("body").removeClass("dashboard-open");
    });
    $(".dashboard-left").click(function (e) {
      e.stopPropagation()
    });

    /*===== select-color =======*/
    $(window).bind("load resize", function () {
      $('select.form-control').css('color', '#BDBDBD');
      $('select.form-control').change(function () {
        let current = $('select.form-control').val();
        if (current != 'null') {
          $(this).css('color', '#363853');
        } else {
          $(this).css('color', '#BDBDBD');
        }
      });
    }).resize();

  }
}
