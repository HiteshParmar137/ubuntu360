import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { ProjectEditComponent } from './project-edit/project-edit.component';
import { ProjectsComponent } from './projects.component';
import { ViewComponent } from './view/view.component';
import { ThankYouComponent } from './thank-you/thank-you.component';
import { DefaultLayoutComponent, DefaultWithoutLoginLayoutComponent } from 'src/app/containers';
import { ProjectFollowedComponent } from './project-followed/project-followed.component';
import { ProjectCompletedComponent } from './project-completed/project-completed.component';
import { AuthGuard } from '../auth/auth.guard';
const routes: Routes = [
  {
    path: '',
    component: DefaultLayoutComponent,
    data: {
      title: 'User Projects'
    },
    children: [
      {
        path: 'list',
        canActivate: [AuthGuard],
        component: ProjectsComponent,
        data: {
          title: 'Project List'
        }
      },
      {
        path: 'details/:id/:referrer',
        canActivate: [AuthGuard],
        component: ViewComponent,
        data: {
          title: 'Project Details'
        }
      },      
      {
        path: 'follow',
        canActivate: [AuthGuard],
        component: ProjectFollowedComponent,
        data: {
          title: 'Followed Project List'
        }
      },
      {
        path: 'complete',
        canActivate: [AuthGuard],
        component: ProjectCompletedComponent,
        data: {
          title: 'Followed Project List'
        }
      },
    ]
  },
  {
    path: '',
    component: DefaultWithoutLoginLayoutComponent,
    data: {
      title: 'User Projects'
    },
    children: [
      {
        path: 'add',
        canActivate: [AuthGuard],
        component: ProjectEditComponent,
        data: {
          title: 'Add Project',
        },
      },
      {
        path: 'edit/:id',
        canActivate: [AuthGuard],
        component: ProjectEditComponent,
        data: {
          title: 'Edit Project',
        },
      },
      
      {
        path: 'donation/thank-you',
        component: ThankYouComponent,
        data: {
          title: 'Project View'
        }
      }      
    ]
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class ProjectsRoutingModule { }
