import { ProjectReportComponent } from './views/project-report/project-report.component';
import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SecureInnerPagesGuard } from './views/auth/secure.auth';
import { AuthGuard } from './views/auth/auth.guard';
import { DefaultLayoutComponent } from './containers';
import { LoginComponent } from './views/auth/login/login.component';
import { DashboardComponent } from './views/dashboard/dashboard.component';
import { ChangePasswordComponent } from './views/change-password/change-password.component';
import { ForgetPasswordComponent } from './views/auth/forget-password/forget-password.component';
import { ResetPasswordComponent } from './views/auth/reset-password/reset-password.component';
import { EditUserComponent } from './views/edit-user/edit-user.component';
import { SiteSettingsComponent } from './views/site-settings/site-settings.component';
import { FeedbackComponent } from './views/feedback/feedback.component';
import { TransactionComponent } from './views/transaction/transaction.component';
import { EsgReportComponent } from './views/esg-report/esg-report.component';
import { SubscriptionComponent } from './views/subscription/subscription.component';
import { UserReportComponent } from './views/user-report/user-report.component';
const routes: Routes = [
  {
    path: '',
    component: DefaultLayoutComponent,
    data: {
      title: 'Home'
    },
    children: [
      {
        path: '',
        component: DashboardComponent,
        canActivate: [AuthGuard],
        data: {
          title: 'Dashboard',
          roleCode: 'admin'
        }
      },
      {
        path: 'dashboard',
        component: DashboardComponent,
        canActivate: [AuthGuard],
        data: {
          title: 'Dashboard',
          roleCode: 'admin'
        }
      },
      {
        path: 'user-management/users',
        canActivate: [AuthGuard],
        loadChildren: () =>
          import('./views/users/users.module').then((m) => m.UsersModule)
      },
      {
        path: 'user-management/corporate-users',
        canActivate: [AuthGuard],
        loadChildren: () =>
          import('./views/corporate-user/corporate-user.module').then((m) => m.CorporateUserModule)
      },
      {
        path: 'admin-management/admin-users',
        canActivate: [AuthGuard],
        loadChildren: () =>
          import('./views/admin-users/admin-users.module').then((m) => m.AdminUsersModule)
      },
      {
        path: 'admin-management/user-groups',
        canActivate: [AuthGuard],
        loadChildren: () =>
          import('./views/user-group/user-group.module').then((m) => m.UserGroupModule)
      },
      {
        path: 'change-password',
        component: ChangePasswordComponent,
        canActivate: [AuthGuard],
        data: {
          title: 'Change Password'
        }
      },
      {
        path: 'user-profile',
        component: EditUserComponent,
        canActivate: [AuthGuard],
        data: {
          title: 'Edit Profile'
        }
      },
      {
        path: 'templates-management',
        canActivate: [AuthGuard],
        loadChildren: () =>
          import(`./views/email-template/email-template.module`).then(m => m.EmailTemplateModule)
      },
      {
        path: 'cms-management',
        canActivate: [AuthGuard],
        loadChildren: () =>
          import(`./views/cms/cms.module`).then(m => m.CmsModule)
      },
      {
        path: 'site-settings',
        canActivate: [AuthGuard],
        component: SiteSettingsComponent,
        data: {
          title: 'Site Settings'
        }
      },
      {
        path: 'feedback',
        canActivate: [AuthGuard],
        component: FeedbackComponent,
        data: {
          title: 'Feedback'
        }
      },
      {
        path: 'project',
        canActivate: [AuthGuard],
        loadChildren: () =>
          import(`./views/project/project.module`).then(m => m.ProjectModule)
      },
      {
        path: 'transactions',
        canActivate: [AuthGuard],
        component: TransactionComponent,
        data: {
          title: 'Transactions'
        }
      },
      {
        path: 'esg-report',
        canActivate: [AuthGuard],
        component: EsgReportComponent,
        data: {
          title: 'ESG Report'
        }
      },
      {
        path: 'subscription',
        canActivate: [AuthGuard],
        component: SubscriptionComponent,
        data: {
          title: 'Subscription'
        }
      },
      {
        path: 'project-report',
        canActivate: [AuthGuard],
        component: ProjectReportComponent,
        data: {
          title: 'Project Report'
        }
      },
      {
        path: 'user-report',
        canActivate: [AuthGuard],
        component: UserReportComponent,
        data: {
          title: 'User Report'
        }
      },
    ]
  },
  {
    path: 'login',
    component: LoginComponent,
    canActivate: [SecureInnerPagesGuard],
    data: {
      title: 'Login'
    }
  },
  {
    path: 'forget-password',
    component: ForgetPasswordComponent,
    canActivate: [SecureInnerPagesGuard],
    data: {
      title: 'Forgot Password'
    }
  },
  {
    path: 'reset-password/:resetToken',
    component: ResetPasswordComponent,
    canActivate: [SecureInnerPagesGuard],
    data: {
      title: 'Reset Password'
    }
  },
  {path: '**', redirectTo: 'login'},
];

@NgModule({
  imports: [
    RouterModule.forRoot(routes, {
      scrollPositionRestoration: 'top',
      anchorScrolling: 'enabled',
      initialNavigation: 'enabledBlocking'
    })
  ],
  exports: [RouterModule]
})
export class AppRoutingModule {
}
