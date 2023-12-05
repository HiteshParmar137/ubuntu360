import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { ApiOperationManagerService } from 'src/app/services/api/operation-manager/api/api-operation-manager.service';
import { ToastrService } from 'ngx-toastr';
import { Constants } from 'src/app/services/constants';
import { Subject } from 'rxjs';
import Swal from 'sweetalert2';
import { NgxSpinnerService } from 'ngx-spinner';
@Injectable({
  providedIn: 'root',
})
export class GeneralService {
  private apiDataTrigger = new Subject<any>();
  public apiDataListener = this.apiDataTrigger.asObservable();
  constructor(
    public router: Router,
    private apiService: ApiOperationManagerService,
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
  ) { }
  passData(data: any) {
    this.apiDataTrigger.next(data);
  }
  aclChecker(redirect: string, module_name: string, action: string) {
    this.apiService
      .getAclChecker(module_name, action)
      .then((res) => {
        if (res.data.success === true) {
          this.router.navigateByUrl(redirect);
        } else {
          // need to redirect on same page
          this.toastr.error(res.data.message, 'Error');
        }
      })
      .catch((err: any) => {
        this.toastr.error(Constants.API_ERROR, 'Error');
      });
  }
  getErrorMsg(responseObj: any) {
    let convertErrorMessage = Object.values(responseObj);
    if (typeof responseObj === 'object') {
      let convertArrayMessage: any[] = [];
      let i = 1;
      for (let convert of convertErrorMessage) {
        let c = i + '.' + convert + `</br>`;
        i++;
        convertArrayMessage.push(c);
      }
      if (convertArrayMessage.length === 1) {
        let singleMessage: any = Object.values(responseObj)[0];
        this.toastr.error(singleMessage, 'error');
      } else {
        let errorMessage = convertArrayMessage.join(' ');
        this.toastr.error(errorMessage, 'error', {
          enableHtml: true,
        });
      }
    } else {
      this.toastr.error(responseObj, 'error');
    }
  }

  public async openDeleteConfirmationDialog(deleteArray: any) {
    let response: any = false;
    return Swal.fire({
      title: deleteArray.title,
      text: deleteArray.text,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, go ahead.',
      cancelButtonText: 'No, let me think',
    }).then((result) => {
      return result.value;
    });
  }

  async showSpinner() {
    await this.spinner.show();
  }
  async hideSpinner() {
    await this.spinner.hide();
  }
}
