import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { RouterModule, Routes } from '@angular/router';
import { CorporateUserComponent } from './corporate-user.component';
import { DetailsComponent } from './details/details.component';
import { ProjectsComponent } from './projects/projects.component';
import { ProjectFollowedComponent } from '../users/project-followed/project-followed.component';
import { AuthGuard } from '../auth/auth.guard';
const routes: Routes = [
  {
    path: '',
    component: CorporateUserComponent,
    canActivate: [AuthGuard],
    data: {
      title: 'Corporate Users'
    }
  },
  {
    path: 'details/:id',
    component: DetailsComponent,
    canActivate: [AuthGuard],
    data: {
      title: 'User Details',
    },
  },
  {
    path: 'project/list/:id',
    component: ProjectsComponent,
    canActivate: [AuthGuard],
    data: {
      title: 'Projct List',
    },
  },
  {
    path: 'project/follow/:id',
    component: ProjectFollowedComponent,
    canActivate: [AuthGuard],
    data: {
      title: 'Projct List',
    },
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class CorporateUserRoutingModule { }
