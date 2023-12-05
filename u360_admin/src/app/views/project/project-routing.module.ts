import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ProjectEditComponent } from './project-edit/project-edit.component';
import { ProjectComponent } from './project.component';
import { ProjectDetailsComponent } from './project-details/project-details.component';
import { DonationsComponent } from './donations/donations.component';
import { AuthGuard } from '../auth/auth.guard';
const routes: Routes = [
  {
    path: '',
    component: ProjectComponent,
    canActivate: [AuthGuard],
    data: {
      title: 'Projects'
    }
  },
  {
    path: 'edit/:id',
    component: ProjectEditComponent,
    canActivate: [AuthGuard],
    data: {
      title: 'Projects Edit'
    }
  },
  {
    path: 'details/:id',
    component: ProjectDetailsComponent,
    canActivate: [AuthGuard],
    data: {
      title: 'Projects Details'
    }
  },
  {
    path: 'donation/:id',
    component: DonationsComponent,
    canActivate: [AuthGuard],
    data: {
      title: 'Project Donation'
    }
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ProjectRoutingModule { }
