import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { CorporateUserRoutingModule } from './corporate-user-routing.module';
import { CorporateUserComponent } from './corporate-user.component';
import { DetailsComponent } from './details/details.component';
import { ProjectsComponent } from './projects/projects.component';
import {
  ButtonModule,
  FormModule,
  GridModule
} from '@coreui/angular';
import { NgxPaginationModule } from 'ngx-pagination';
import { HeaderTabComponent } from './header-tab/header-tab.component';
@NgModule({
  declarations: [
    CorporateUserComponent,
    DetailsComponent,
    ProjectsComponent,
    HeaderTabComponent
  ],
  imports: [
    CommonModule,
    CorporateUserRoutingModule,
    ReactiveFormsModule,
    FormsModule,
    ButtonModule,
    FormModule,
    GridModule,
    NgxPaginationModule
  ]
})
export class CorporateUserModule { }
