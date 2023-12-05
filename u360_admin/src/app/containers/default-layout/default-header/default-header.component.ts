import { Component, Input } from '@angular/core';
import { HeaderComponent } from '@coreui/angular';
import { AuthService } from 'src/app/views/auth/auth.service';
import { GeneralService } from 'src/app/services/general.service';
@Component({
  selector: 'app-default-header',
  templateUrl: './default-header.component.html',
})
export class DefaultHeaderComponent extends HeaderComponent {

  @Input() sidebarId: string = "sidebar";

  userDetail: any;
  userProfile: string | undefined;
  userName: string = '';
  groupId: string = "";
  userNameSubscriber: any;

  constructor(
    public authService: AuthService,
    private GeneralService: GeneralService
    ) {
    super();
    this.userNameSubscriber = this.GeneralService.apiDataListener.subscribe((data: any) => {
      this.userName = data.name;
      this.userProfile = data.profile_image;
      this.groupId = data.user_group_id;
    });
  }
  ngOnInit(): void {
    this.userName = localStorage.getItem('name') || '';
    this.userProfile = localStorage.getItem('image') || '';
    this.groupId = localStorage.getItem('user_group_id') || '';
  }
}
