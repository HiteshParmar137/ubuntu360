import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { CmsAddComponent } from './cms-add/cms-add.component';
import { CmsEditComponent } from './cms-edit/cms-edit.component';
import { CmsComponent } from './cms.component';
const routes: Routes = [
  {
    path: '',
    component: CmsComponent,
    data: {
      title: 'CMS Management'
    }
  },
  {
    path: 'add',
    component: CmsAddComponent,
    data: {
      title: 'Add CMS',
    },
  },
  {
    path: 'edit/:id',
    component: CmsEditComponent,
    data: {
      title: 'Edit CMS',
    },
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class CmsRoutingModule { }
