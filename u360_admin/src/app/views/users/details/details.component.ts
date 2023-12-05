import { Component, OnInit } from '@angular/core';
import { UntypedFormBuilder, UntypedFormGroup, Validators } from "@angular/forms";
import { ActivatedRoute, Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { NgxSpinnerService } from 'ngx-spinner';
import { AppSettingsService } from 'src/app/app-settings.service';
import { GeneralService } from 'src/app/services/general.service';
@Component({
  selector: 'app-details',
  templateUrl: './details.component.html',
  styleUrls: ['./details.component.scss']
})
export class DetailsComponent implements OnInit {
  id: any;
  userEdit: UntypedFormGroup = new UntypedFormGroup({});
  userDetails: any = [];
  constructor(
    private formBuilder: UntypedFormBuilder,
    private toastr: ToastrService,
    public router: Router,
    private route: ActivatedRoute,
    private appSettingsService: AppSettingsService,
    private generalService: GeneralService,
    private spinner: NgxSpinnerService) {
    this.route.paramMap.subscribe(params => {
      this.id = params.get('id');
    });
  }

  ngOnInit(): void {
    this.getUserDetail();
  }
  getUserDetail() {
    this.appSettingsService.getUserDetails(this.id).subscribe((res) => {
      if (res.success == true) {
        console.log(this.userDetails);
        this.userDetails = res.data.userDetails;
        this.toastr.success(res.message, "Success");
      } else {
        this.toastr.success(res.message, "error");
      }
    },
    (error) => {     
      this.spinner.hide();        
      this.generalService.getErrorMsg(error.error.message);
    });
  }
}
