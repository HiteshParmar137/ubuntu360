import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { EmailTemplateAddComponent } from './email-template-add/email-template-add.component';
import { EmailTemplateEditComponent } from './email-template-edit/email-template-edit.component';
import { EmailTemplateRoutingModule } from './email-template-routing.module';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { ButtonModule, FormModule, GridModule } from '@coreui/angular';
import { CKEditorModule } from 'ckeditor4-angular';


@NgModule({
  declarations: [
    EmailTemplateAddComponent,
    EmailTemplateEditComponent
  ],
  imports: [
    CommonModule,
    EmailTemplateRoutingModule,
    ReactiveFormsModule,
    FormsModule,
    ButtonModule,
    FormModule,
    GridModule,
    CKEditorModule
  ]
})
export class EmailTemplateModule { }
