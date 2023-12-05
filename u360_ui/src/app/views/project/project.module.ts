import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { ProjectRoutingModule } from './project-routing.module';
import { ProjectDetailsComponent } from './project-details/project-details.component';
import { MatTabsModule } from '@angular/material/tabs';
import { NgxPaginationModule } from 'ngx-pagination';
import { VolunteerComponent } from './volunteer/volunteer.component';
import { FormsModule,ReactiveFormsModule  } from '@angular/forms';
import { ButtonModule, FormModule, GridModule } from '@coreui/angular';
import { CarouselModule } from 'ngx-owl-carousel-o';
import { NgbRatingModule, NgbModule } from '@ng-bootstrap/ng-bootstrap';
@NgModule({
  declarations: [
    ProjectDetailsComponent,
    VolunteerComponent
  ],
  imports: [
    CommonModule,
    ProjectRoutingModule,
    MatTabsModule,
    NgxPaginationModule,
    FormsModule,
    ReactiveFormsModule,
    ButtonModule,
    FormModule,
    GridModule,
    CarouselModule,
    NgbRatingModule,
    NgbModule,
  ]
})
export class ProjectModule { }
