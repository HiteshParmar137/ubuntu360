import { Component, OnInit } from '@angular/core';
import {
  AbstractControl,
  UntypedFormBuilder,
  UntypedFormGroup,
  Validators,
} from '@angular/forms';
import { Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { NgxSpinnerService } from 'ngx-spinner';
import { AppSettingsService } from "src/app/app-settings.service";
import { GeneralService } from 'src/app/services/general.service';

@Component({
  selector: 'app-esg-reports',
  templateUrl: './esg-reports.component.html',
  styleUrls: ['./esg-reports.component.scss']
})
export class EsgReportsComponent implements OnInit {
  EsgReportsEmailForm: UntypedFormGroup = new UntypedFormGroup({});
  submitted = false;

  constructor(
    private formBuilder: UntypedFormBuilder,
    public router: Router,
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private appSettingsService: AppSettingsService,
    private generalService: GeneralService
  ) { }

  ngOnInit(): void {
    this.generateFormObj();
  }

  get f(): { [key: string]: AbstractControl } {
    return this.EsgReportsEmailForm.controls;
  }

  generateFormObj() {
    this.EsgReportsEmailForm = this.formBuilder.group({
      email: ['', [Validators.required, Validators.email]],
    });
  }

  onSubmit(): void {
    this.submitted = true;
    if (this.EsgReportsEmailForm.invalid) {
      return;
    }
    this.appSettingsService.esgReportsEmail(this.EsgReportsEmailForm.value).subscribe({
      next:(res: any) => {
        if (res) {
          if (res.success) {
            this.toastr.success(res.message, "Success");
          } else {
            this.generalService.getErrorMsg(res.message);
          }
        }
      }
    });
  }
}

