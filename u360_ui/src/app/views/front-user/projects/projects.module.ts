import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { ProjectsRoutingModule } from './projects-routing.module';
import { ProjectAddComponent } from './project-add/project-add.component';
import { ProjectEditComponent } from './project-edit/project-edit.component';
import { FormsModule,ReactiveFormsModule  } from '@angular/forms';
import { ButtonModule, FormModule, GridModule } from '@coreui/angular';
import { LightboxModule } from 'ngx-lightbox';
import { MatTabsModule } from '@angular/material/tabs';
import { ViewComponent } from './view/view.component';
import {MatSelectModule} from '@angular/material/select';
import { ThankYouComponent } from './thank-you/thank-you.component';
import { NgxBootstrapMultiselectModule } from 'ngx-bootstrap-multiselect';
import { ProjectFollowedComponent } from './project-followed/project-followed.component';
import { ProjectCompletedComponent } from './project-completed/project-completed.component';
import { NgxPaginationModule } from 'ngx-pagination';
import { CarouselModule } from 'ngx-owl-carousel-o';
@NgModule({
  declarations: [
    ProjectAddComponent,
    ProjectEditComponent,
    ViewComponent,
    ThankYouComponent,
    ProjectFollowedComponent,
    ProjectCompletedComponent
  ],
  imports: [
    CommonModule,
    ProjectsRoutingModule,
    FormsModule,
    ReactiveFormsModule,
    ButtonModule,
    FormModule,
    GridModule,
    LightboxModule,
    MatTabsModule,
    MatSelectModule,
    NgxBootstrapMultiselectModule,
    NgxPaginationModule,
    CarouselModule
  ]
})
export class ProjectsModule { }
