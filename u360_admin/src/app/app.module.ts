import { NgModule } from '@angular/core';
import { BrowserModule, Title } from '@angular/platform-browser';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { HttpClientModule,HTTP_INTERCEPTORS  } from '@angular/common/http';
import { AuthInterceptor } from './views/auth/auth.interceptor';
import { NgxPaginationModule } from 'ngx-pagination';
import { CarouselModule } from 'ngx-owl-carousel-o';
// import {
//   PERFECT_SCROLLBAR_CONFIG,
//   PerfectScrollbarConfigInterface,
//   PerfectScrollbarModule,
// } from 'ngx-perfect-scrollbar';

// Import routing module
import { AppRoutingModule } from './app-routing.module';

// Import app component
import { AppComponent } from './app.component';

// Import containers
import {
  DefaultFooterComponent,
  DefaultHeaderComponent,
  DefaultLayoutComponent,
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
import { LoginComponent } from './views/auth/login/login.component';
import { DashboardComponent } from './views/dashboard/dashboard.component';
import { CoreService } from './services/api/core/core.service';
import { RequestService } from './services/api/core/implementations/request.service';
import { ApiOperationManagerService } from './services/api/operation-manager/api/api-operation-manager.service';
import { OperationManagerService } from './services/api/operation-manager/implementations/operation-manager.service';
import { ChangePasswordComponent } from './views/change-password/change-password.component';
import { ForgetPasswordComponent } from './views/auth/forget-password/forget-password.component';
import { ResetPasswordComponent } from './views/auth/reset-password/reset-password.component';
import { EditUserComponent } from './views/edit-user/edit-user.component';
import { ChartjsModule } from '@coreui/angular-chartjs';
import { EmailTemplateComponent } from './views/email-template/email-template.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { AdminUsersComponent } from './views/admin-users/admin-users.component';
import { CmsComponent } from './views/cms/cms.component';
import { SiteSettingsComponent } from './views/site-settings/site-settings.component';
import { FeedbackComponent } from './views/feedback/feedback.component';
import { ConfirmationDialogService } from './views/confirmation-dialog/confirmation-dialog.service';
import { ProjectComponent } from './views/project/project.component';
import { TransactionComponent } from './views/transaction/transaction.component';
import { HighchartsChartModule } from 'highcharts-angular';
import { EsgReportComponent } from './views/esg-report/esg-report.component';
import { SubscriptionComponent } from './views/subscription/subscription.component';
import { ProjectReportComponent } from './views/project-report/project-report.component';
import { UserReportComponent } from './views/user-report/user-report.component';
// import { CorporateUserComponent } from './views/corporate-user/corporate-user.component';
// import { DetailsComponent } from './views/corporate-user/details/details.component';
// import { ProjectsComponent } from './views/corporate-user/projects/projects.component';


// const DEFAULT_PERFECT_SCROLLBAR_CONFIG: PerfectScrollbarConfigInterface = {
//   suppressScrollX: true,
// };

const APP_CONTAINERS = [
  DefaultFooterComponent,
  DefaultHeaderComponent,
  DefaultLayoutComponent,
];

@NgModule({
  declarations: [DashboardComponent, ForgetPasswordComponent, ResetPasswordComponent, LoginComponent, AppComponent, ...APP_CONTAINERS, ChangePasswordComponent, EditUserComponent, EmailTemplateComponent, AdminUsersComponent,CmsComponent, SiteSettingsComponent, FeedbackComponent,ProjectComponent, TransactionComponent, EsgReportComponent, SubscriptionComponent, ProjectReportComponent, UserReportComponent],
  imports: [
    HttpClientModule,
    BrowserModule,
    BrowserAnimationsModule,
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
    HighchartsChartModule,
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
    IconSetService,
    Title,
    ConfirmationDialogService
  ],
  bootstrap: [AppComponent],
})
export class AppModule {
}
