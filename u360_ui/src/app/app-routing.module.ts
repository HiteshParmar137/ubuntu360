import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { SecureInnerPagesGuard } from './views/front-user/auth/secure.auth';
import { AuthGuard } from './views/front-user/auth/auth.guard';
import { DefaultLayoutComponent, DefaultWithoutLoginLayoutComponent } from './containers';
import { LoginComponent } from './views/front-user/auth/login/login.component';
import { DashboardComponent } from './views/front-user/dashboard/dashboard.component';
import { ChangePasswordComponent } from './views/front-user/change-password/change-password.component';
import { ForgetPasswordComponent } from './views/front-user/auth/forget-password/forget-password.component';
import { ResetPasswordComponent } from './views/front-user/auth/reset-password/reset-password.component';
import { RegisterComponent } from './views/front-user/auth/register/register.component';
import { VerifyEmailComponent } from './views/front-user/auth/verify-email/verify-email.component';
import { SignupStepComponent } from './views/front-user/auth/register/signup-step/signup-step.component';
import { EditUserComponent } from './views/front-user/edit-user/edit-user.component';
import { EsgReportsComponent } from './views/esg-reports/esg-reports.component';
import { HomeComponent } from './views/home/home.component';
import { DonationComponent } from './views/front-user/donation/donation/donation.component';
import { AboutUsComponent } from "./views/about-us/about-us.component";
import {CommunityComponent} from "./views/community/community.component";
import {DonateComponent} from "./views/donate/donate.component";
import {VolunteerComponent} from "./views/volunteer/volunteer.component";
import { PrivacyPolicyComponent } from './views/privacy-policy/privacy-policy.component';
import { CommunityComponent as UserCommunityComponent } from './views/front-user/community/community.component';
import { CmsPageComponent } from './views/cms-page/cms-page.component';
import { TermsConditionComponent } from './views/terms-condition/terms-condition.component';

const routes: Routes = [
  {
    path: '',
    component: DefaultWithoutLoginLayoutComponent,
    data: {
      title: 'Home'
    },
    children: [
      {
        path: '',
        component: HomeComponent,
        data: {
          title: 'Home'
        }
      },
      {
        path: 'home',
        component: HomeComponent,
        data: {
          title: 'Home'
        }
      },
      {
        path: 'privacy-policy',
        component: PrivacyPolicyComponent,
        data: {
          title: 'Privacy Policy'
        }
      },
      {
        path: 'terms-condition',
        component: TermsConditionComponent,
        data: {
          title: 'Terms & Condition'
        }
      },
      {
        path: 'explore',
        component: CmsPageComponent,
        data: {
          title: 'Explore'
        }
      },
      {
        path: 'about-us',
        component: AboutUsComponent,
        data: {
          title: 'About Us'
        }
      },
      {
        path: 'community',
        component: CommunityComponent,
        data: {
          title: 'Community'
        }
      },
      {
        path: 'donate',
        component: DonateComponent,
        data: {
          title: 'Donate'
        }
      },
      {
        path: 'volunteer',
        component: VolunteerComponent,
        data: {
          title: 'Volunteer'
        }
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
      {
        path: 'register',
        component: RegisterComponent,
        data: {
          title: 'Register'
        }
      },
      {
        path: 'signup-step',
        component: SignupStepComponent,
        data: {
          title: 'Signup Details'
        }
      },
      {
        path: 'verify-email/:verifyToken',
        component: VerifyEmailComponent,
        canActivate: [SecureInnerPagesGuard],
        data: {
          title: 'Verify Email'
        }
      },
      {
        path: 'projects',
        loadChildren: () =>
          import(`./views/project/project.module`).then(m => m.ProjectModule),
        data: {
          title: 'Project List'
        }
      },
      {
        path: 'esg-reports',
        component: EsgReportsComponent,
        data: {
          title: 'ESG Reports'
        }
      },
    ]
  },
  {
    path: 'user/projects',
    canActivate: [AuthGuard],
    loadChildren: () =>
      import(`./views/front-user/projects/projects.module`).then(m => m.ProjectsModule)
  },
  
  {
    path: '',
    component: DefaultLayoutComponent,
    data: {
      title: 'Home'
    },
    children: [
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
        path: 'user/change-password',
        component: ChangePasswordComponent,
        canActivate: [AuthGuard],
        data: {
          title: 'Change Password'
        }
      },
      {
        path: 'user/profile',
        component: EditUserComponent,
        canActivate: [AuthGuard],
        data: {
          title: 'Edit Profile'
        }
      },
      {
        path: 'user/donation',
        component: DonationComponent,
        canActivate: [AuthGuard],
        data: {
          title: 'Edit Profile'
        }
      },
      // {
      //   path: 'user/project-donation',
      //   component: ProjectDonationComponent,
      //   canActivate: [AuthGuard],
      //   data: {
      //     title: 'Edit Profile'
      //   }
      // },
      {
        path: 'user/community',
        component: UserCommunityComponent,
        canActivate: [AuthGuard],
        data: {
          title: 'Community'
        }
      },
    ]
  },
   
  {path: '**', redirectTo: 'home'},
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
