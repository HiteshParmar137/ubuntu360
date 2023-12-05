import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { CmsRoutingModule } from './cms-routing.module';
//import { CmsComponent } from './cms.component';
import { CmsAddComponent } from './cms-add/cms-add.component';
import { CmsEditComponent } from './cms-edit/cms-edit.component';
import { FormsModule,ReactiveFormsModule  } from '@angular/forms';
import { ButtonModule, FormModule, GridModule } from '@coreui/angular';
import { CKEditorModule } from 'ckeditor4-angular';

@NgModule({
  declarations: [
    //CmsComponent,
    CmsAddComponent,
    CmsEditComponent
  ],
  imports: [
    CommonModule,
    CmsRoutingModule,
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    ButtonModule,
    FormModule,
    GridModule,
    CKEditorModule
  ]
})
export class CmsModule { }
