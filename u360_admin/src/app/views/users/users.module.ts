import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { UsersRoutingModule } from './users-routing.module';
import { UsersComponent } from './users.component';
import { UserAddComponent } from './user-add/user-add.component';
import { UserEditComponent } from './user-edit/user-edit.component';
import { ButtonModule, FormModule, GridModule } from '@coreui/angular';
import { DetailsComponent } from './details/details.component';
import { ProjectsComponent } from './projects/projects.component';
import { NgxPaginationModule } from 'ngx-pagination';
import { ProjectFollowedComponent } from './project-followed/project-followed.component';
import { HeaderTabComponent } from './header-tab/header-tab.component';
@NgModule({
  declarations: [
    UsersComponent,
    UserAddComponent, 
    UserEditComponent, DetailsComponent, ProjectsComponent, ProjectFollowedComponent, HeaderTabComponent
  ],
  imports: [
    CommonModule,
    UsersRoutingModule,
    ReactiveFormsModule,
    FormsModule,
    ButtonModule,
    FormModule,
    GridModule,
    NgxPaginationModule
  ]
})
export class UsersModule { }
