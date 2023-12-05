import { Component, Input, OnInit } from '@angular/core';
import { AuthService } from 'src/app/views/front-user/auth/auth.service';
import { GeneralService } from '../../../services/general.service';
import * as $ from 'jquery';
@Component({
  selector: 'app-default-without-login-header',
  templateUrl: './default-without-login-header.component.html',
})
export class DefaultWithoutLoginHeaderComponent implements OnInit {

  @Input() sidebarId: string = "sidebar";

  userDetail: any;
  userProfile: string | undefined;
  userName: string = '';
  userNameSubscriber: any;
  siteSettingSubscriber: any;
  groupId: string = "";
  userId: string = "";
  userLoggedIn = false;
  phone_no: any;
  email: any;
  whatsapp_no: any;
  instagram: any;
  twitter: any;
  facebook: any;
  youtube: any;
  visit_us: any;
  constructor(
    public authService: AuthService,
    private generalService: GeneralService
  ) {
    this.userNameSubscriber = this.generalService.apiDataListener.subscribe((data: any) => {
      
      this.userName = data.name;
      this.userProfile = data.profile_image;
      this.groupId = data.user_group_id;
    });
    this.siteSettingSubscriber = this.generalService.siteSettingsDataListener.subscribe((data: any) => {
      this.phone_no = data.phone_no ?? '';
      this.email = data.email ?? '';
      this.whatsapp_no = data.whatsapp_no ?? '';
      this.twitter = data.twitter ?? '';
      this.instagram = data.instagram ?? '';
      this.facebook = data.facebook ?? '';
      this.youtube = data.youtube ?? '';
      this.visit_us = data.visit_us ?? '';
    });
  }
  ngOnInit() {
    this.userName = localStorage.getItem('name') ?? '';
    this.userProfile = localStorage.getItem('image') ?? '';
    this.groupId = localStorage.getItem('user_group_id') ?? '';
    this.userId = localStorage.getItem('userId') ?? '';
    if (this.userId) {
      this.userLoggedIn = true;
    } else {
      this.userLoggedIn = false;
    }
    /*===== Burger--menu--open =======*/

    $('.burger__menu').click(function () {
      $("body").toggleClass("menu-open");
    });
    $(document).on('click', function () {
      $("body").removeClass("menu-open");
    });
    $(".site-menu ul li a").on('click', function () {
      $("body").removeClass("menu-open");
    });
    $(".site-menu, .burger__menu").on('click', function (e) {
      e.stopPropagation()
    });


    /*===== sub-menu--open =======*/


    if (<any>($(window).width()) < 991) {
      $(".site-menu ul li.menu-item-children").append('<span class="child-trigger"></span>');
      $('.child-trigger').click(function () {
        jQuery(this).siblings('.sub-menu').slideToggle(500);
        jQuery(this).toggleClass('child-open');
        return false;
      });

      $(window).on('load resize', () => {
        /*===== user-icon-mobile =======*/
        $(document).on('click', '.user-icon-mobile .user-icon', function () {
          $(".user-icon-mobile .logout-dropdown").slideDown(500);
        });
        $(document).on('click', function () {
          $(".logout-dropdown").slideUp(500);
        });
        $(document).on('click', '.user-icon-mobile', function (e) {
          e.stopPropagation()
        });
      }).resize();
    };


  }
}
