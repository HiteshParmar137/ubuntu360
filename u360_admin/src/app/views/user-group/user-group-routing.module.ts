import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { UserGroupComponent } from './user-group.component';
import { UserGroupEditComponent } from './user-group-edit/user-group-edit.component';
import { UserGroupAddComponent } from './user-group-add/user-group-add.component';
import { AuthGuard } from '../auth/auth.guard';
const routes: Routes = [
  {
    path: '',
    component: UserGroupComponent,
    canActivate: [AuthGuard],
    data: {
      title: 'User Groups'
    }
  },
  {
    path: 'edit/:id',
    component: UserGroupEditComponent,
    canActivate: [AuthGuard],
    data: {
      title: 'Edit User Group'
    }
  },
  {
    path: 'add',
    component: UserGroupAddComponent,
    canActivate: [AuthGuard],
    data: {
      title: 'Add User Group'
    }
  }

];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class UserGroupRoutingModule { }
