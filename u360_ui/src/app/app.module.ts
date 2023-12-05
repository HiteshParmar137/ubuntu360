import { NgModule } from '@angular/core';
import { BrowserModule, Title } from '@angular/platform-browser';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { HttpClientModule, HTTP_INTERCEPTORS } from '@angular/common/http';
import { AuthInterceptor } from './views/front-user/auth/auth.interceptor';
import { NgxPaginationModule } from 'ngx-pagination';
import { SocialLoginModule, SocialAuthServiceConfig,GoogleLoginProvider, FacebookLoginProvider } from '@abacritt/angularx-social-login';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';

// Import routing module
import { AppRoutingModule } from './app-routing.module';

// Import app component
import { AppComponent } from './app.component';

// Import containers
import {
  DefaultLayoutComponent,
  DefaultWithoutLoginFooterComponent,
  DefaultWithoutLoginHeaderComponent,
  DefaultWithoutLoginLayoutComponent,
} from './containers';

import {
  AvatarModule,
  BadgeModule,
  BreadcrumbModule,
  DropdownModule,
  FooterModule,
  FormModule,
  GridModule,
  HeaderModule,
  NavModule,
  SharedModule,
  SidebarModule,
  TabsModule,
  UtilitiesModule

} from '@coreui/angular';
import { ToastrModule } from 'ngx-toastr';
import { NgxSpinnerModule } from 'ngx-spinner';
import { IconModule, IconSetService } from '@coreui/icons-angular';
import { LoginComponent } from './views/front-user/auth/login/login.component';
import { DashboardComponent } from './views/front-user/dashboard/dashboard.component';
import { CoreService } from './services/api/core/core.service';
import { RequestService } from './services/api/core/implementations/request.service';
import { ApiOperationManagerService } from './services/api/operation-manager/api/api-operation-manager.service';
import { OperationManagerService } from './services/api/operation-manager/implementations/operation-manager.service';
import { ChangePasswordComponent } from './views/front-user/change-password/change-password.component';
import { ForgetPasswordComponent } from './views/front-user/auth/forget-password/forget-password.component';
import { ResetPasswordComponent } from './views/front-user/auth/reset-password/reset-password.component';
import { ChartjsModule } from '@coreui/angular-chartjs';
import { NgbModule,NgbRatingModule } from '@ng-bootstrap/ng-bootstrap';
import { RegisterComponent } from './views/front-user/auth/register/register.component';
import { SignupStepComponent } from './views/front-user/auth/register/signup-step/signup-step.component';
import { EditUserComponent } from './views/front-user/edit-user/edit-user.component';
import { NgxSliderModule } from '@angular-slider/ngx-slider';
import { ProjectsComponent } from './views/front-user/projects/projects.component';
import { ProjectComponent } from './views/project/project.component';
import { EsgReportsComponent } from './views/esg-reports/esg-reports.component';
import { HighchartsChartModule } from 'highcharts-angular';
import { CategoryDetailsComponent } from './views/front-user/auth/register/category-details/category-details.component';
import { HomeComponent } from './views/home/home.component';
import { DonationComponent } from './views/front-user/donation/donation/donation.component';
import { environment } from 'src/environments/environment';
import { AboutUsComponent } from './views/about-us/about-us.component';
import { CommunityComponent } from './views/community/community.component';
import { DonateComponent } from './views/donate/donate.component';
import { VolunteerComponent } from './views/volunteer/volunteer.component';
import { PrivacyPolicyComponent } from './views/privacy-policy/privacy-policy.component';
import { CarouselModule } from 'ngx-owl-carousel-o';
import { CommunityComponent as UserCommunityComponent } from './views/front-user/community/community.component';
import { CmsPageComponent } from './views/cms-page/cms-page.component';
import { TermsConditionComponent } from './views/terms-condition/terms-condition.component';
const GOOGLE_CLIENT_ID = environment.google_client_Id;
const FB_CLIENT_ID = environment.fb_client_Id;

const APP_CONTAINERS = [
  DefaultLayoutComponent,
  DefaultWithoutLoginFooterComponent,
  DefaultWithoutLoginHeaderComponent,
  DefaultWithoutLoginLayoutComponent,
];

@NgModule({
  declarations: [DashboardComponent, ForgetPasswordComponent, ResetPasswordComponent, LoginComponent, AppComponent, ...APP_CONTAINERS, ChangePasswordComponent, RegisterComponent, EditUserComponent, SignupStepComponent, ProjectsComponent, ProjectComponent, EsgReportsComponent, CategoryDetailsComponent, HomeComponent, DonationComponent, AboutUsComponent, CommunityComponent, DonateComponent, VolunteerComponent, PrivacyPolicyComponent, UserCommunityComponent, CmsPageComponent, TermsConditionComponent],
  imports: [
    HttpClientModule,
    BrowserModule,
    BrowserAnimationsModule,
    MatInputModule,
    MatButtonModule,
    AppRoutingModule,
    AvatarModule,
    BreadcrumbModule,
    FooterModule,
    DropdownModule,
    GridModule,
    HeaderModule,
    SidebarModule,
    IconModule,
    // PerfectScrollbarModule,
    NavModule,
    FormModule,
    UtilitiesModule,
    ReactiveFormsModule,
    FormsModule,
    SidebarModule,
    SharedModule,
    TabsModule,
    BadgeModule,
    ToastrModule.forRoot(),
    NgxSpinnerModule,
    ChartjsModule,
    NgbModule,
    NgxPaginationModule,
    SocialLoginModule,
    NgxSliderModule,
    HighchartsChartModule,    
    NgbRatingModule,
    CarouselModule
  ],
  providers: [
    {
      provide: CoreService,
      useClass: RequestService,
    },
    {
      provide: ApiOperationManagerService,
      useClass: OperationManagerService,
    },
    {
      provide: HTTP_INTERCEPTORS,
      useClass: AuthInterceptor,
      multi: true
    },
    {
      provide: 'SocialAuthServiceConfig',
      useValue: {
        autoLogin: false,
        providers: [
          {
            id: GoogleLoginProvider.PROVIDER_ID,
            provider: new GoogleLoginProvider(
              GOOGLE_CLIENT_ID
            )
          },
          {
            id: FacebookLoginProvider.PROVIDER_ID,
            provider: new FacebookLoginProvider(FB_CLIENT_ID)
          }
        ],
        onError: (err) => {
          console.error(err);
        }
      } as SocialAuthServiceConfig,
    },
    IconSetService,
    Title
  ],
  bootstrap: [AppComponent],
})
export class AppModule {
}
