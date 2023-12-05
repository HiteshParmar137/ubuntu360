import { Component, OnInit, Input  } from '@angular/core';
import { AppSettingsService } from 'src/app/app-settings.service';
import { NgxSpinnerService } from 'ngx-spinner';
import { GeneralService } from 'src/app/services/general.service';
@Component({
  selector: 'app-cms-page',
  templateUrl: './cms-page.component.html',
})
export class CmsPageComponent implements OnInit {
  @Input() myVariable: any;
  cmsPageContent:any='';
  slug:any='';
  pageName:any='';
  constructor(
    private appSettingsService: AppSettingsService,
    public generalService: GeneralService,
  ) {
  }

  ngOnInit(): void {
    this.getGetCMSPageContent();
  }
  getGetCMSPageContent() {
    
    const postData = {'slug':this.myVariable};
    this.appSettingsService.getAboutUsPage(postData).subscribe({
      next: (res) => {
        if (res.success === true) {
          this.cmsPageContent=res.data.content;
        } else {
          this.generalService.getErrorMsg(res.message);
        }
      },
      error:(error) => {             
        this.generalService.getErrorMsg(error.error.message);
      }
    });
  }

}
