import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AdminUsersComponent } from './admin-users.component';
import { AdminUserAddComponent } from './admin-user-add/admin-user-add.component';
import { AdminUserEditComponent } from './admin-user-edit/admin-user-edit.component';
import { AuthGuard } from '../auth/auth.guard';
const routes: Routes = [
  {
    path: '',
    component: AdminUsersComponent,
    canActivate: [AuthGuard],
    data: {
      title: 'Admin Users'
    }
  },
  {
    path: 'add',
    component: AdminUserAddComponent,
    canActivate: [AuthGuard],
    data: {
      title: 'Add Admin User',
    },
  },
  {
    path: 'edit/:id',
    component: AdminUserEditComponent,
    canActivate: [AuthGuard],
    data: {
      title: 'Edit Admin User',
    },
  }
]

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class AdminUsersRoutingModule { }
