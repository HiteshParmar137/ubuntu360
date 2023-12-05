import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { AdminUsersRoutingModule } from './admin-users-routing.module';
import { AdminUsersComponent } from './admin-users.component';
import {
  ButtonModule,
  FormModule,
  GridModule
} from '@coreui/angular';
import { AdminUserAddComponent } from './admin-user-add/admin-user-add.component';
import { AdminUserEditComponent } from './admin-user-edit/admin-user-edit.component';
@NgModule({
  declarations: [
    AdminUserAddComponent,
    AdminUserEditComponent
  ],
  imports: [
    CommonModule,
    AdminUsersRoutingModule,
    ReactiveFormsModule,
    FormsModule,
    ButtonModule,
    FormModule,
    GridModule
  ]
})
export class AdminUsersModule { }
