import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { UsersComponent } from './users.component';
import { UserAddComponent } from './user-add/user-add.component';
import { UserEditComponent } from './user-edit/user-edit.component';
import { DetailsComponent } from '../users/details/details.component';
import { ProjectsComponent } from './projects/projects.component';
import { ProjectFollowedComponent } from './project-followed/project-followed.component';
import { AuthGuard } from '../auth/auth.guard';
const routes: Routes = [
  {
    path: '',
    component: UsersComponent,
    canActivate: [AuthGuard],
    data: {
      title: 'Users'
    }
  },
  {
    path: 'edit/:id',
    component: UserEditComponent,
    canActivate: [AuthGuard],
    data: {
      title: 'Edit User',
    },
  },
  {
    path: 'add',
    component: UserAddComponent,
    canActivate: [AuthGuard],
    data: {
      title: 'Add User',
    },
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
      title: 'Followed Projct List',
    },
  },
]


@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class UsersRoutingModule { }
