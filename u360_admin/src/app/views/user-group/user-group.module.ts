import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { UserGroupRoutingModule } from './user-group-routing.module';
import { UserGroupComponent } from './user-group.component';
import { UserGroupAddComponent } from './user-group-add/user-group-add.component';
import { UserGroupEditComponent } from './user-group-edit/user-group-edit.component';
import { ToastrModule } from 'ngx-toastr';
import { NgxSpinnerModule } from 'ngx-spinner';
import {
  FormModule,
  GridModule
} from '@coreui/angular';

@NgModule({
  declarations: [
    UserGroupComponent,
    UserGroupAddComponent,
    UserGroupEditComponent
  ],
  imports: [
    CommonModule,
    UserGroupRoutingModule,
    FormModule,
    GridModule,
    ReactiveFormsModule,
    FormsModule,
    ToastrModule,
    NgxSpinnerModule
  ]
})
export class UserGroupModule { }
