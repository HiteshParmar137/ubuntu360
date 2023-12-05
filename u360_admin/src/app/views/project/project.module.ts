import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { ProjectRoutingModule } from './project-routing.module';
// import { ProjectComponent } from './project.component';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { ButtonModule, FormModule, GridModule } from '@coreui/angular';
import { ProjectAddComponent } from './project-add/project-add.component';
import { ProjectEditComponent } from './project-edit/project-edit.component';
import { ProjectDetailsComponent } from './project-details/project-details.component';
import { FollowersComponent } from './followers/followers.component';
import { DonationsComponent } from './donations/donations.component';
import { NgxPaginationModule } from 'ngx-pagination';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { VolunteerComponent } from './volunteer/volunteer.component';
@NgModule({
  declarations: [
    // ProjectComponent
    ProjectAddComponent,
    ProjectEditComponent,
    ProjectDetailsComponent,
    FollowersComponent,
    DonationsComponent,
    VolunteerComponent
  ],
  imports: [
    CommonModule,
    ProjectRoutingModule,
    FormsModule,
    ReactiveFormsModule,
    ButtonModule,
    FormModule,
    GridModule,
    NgxPaginationModule,
    NgbModule
  ]
})
export class ProjectModule { }
