import { EmailTemplateEditComponent } from './email-template-edit/email-template-edit.component';
import { EmailTemplateAddComponent } from './email-template-add/email-template-add.component';
import { EmailTemplateComponent } from './email-template.component';
import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AuthGuard } from '../auth/auth.guard';
const routes: Routes = [
  {
    path: '',
    component: EmailTemplateComponent,
    canActivate: [AuthGuard],
    data: {
      title: 'Template Management'
    }
  },
  {
    path: 'add',
    component: EmailTemplateAddComponent,
    canActivate: [AuthGuard],
    data: {
      title: 'Add Email Template',
    },
  },
  {
    path: 'edit/:id',
    component: EmailTemplateEditComponent,
    canActivate: [AuthGuard],
    data: {
      title: 'Edit Email Template',
    },
  }
]


@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class EmailTemplateRoutingModule { }
