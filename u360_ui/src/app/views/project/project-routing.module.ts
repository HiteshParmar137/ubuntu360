import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from '../front-user/auth/auth.guard';
import { ProjectDetailsComponent } from './project-details/project-details.component';
import { ProjectComponent } from './project.component';
import { VolunteerComponent } from './volunteer/volunteer.component';
import { CommunityComponent } from '../community/community.component';

const routes: Routes = [
  {
    path: '',
    component: ProjectComponent,
    data: {
      title: 'project list'
    }
  },
  {
    path: 'details/:id',
    component: ProjectDetailsComponent,
    data: {
      title: 'Project Details'
    }
  },
  {
    path: 'volunteer/:id',
    canActivate: [AuthGuard],
    component: VolunteerComponent,
    data: {
      title: 'Volunteer'
    }
  },
  {
    path: 'community',
    canActivate: [AuthGuard],
    component: CommunityComponent,
    data: {
      title: 'Volunteer'
    }
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ProjectRoutingModule { }
