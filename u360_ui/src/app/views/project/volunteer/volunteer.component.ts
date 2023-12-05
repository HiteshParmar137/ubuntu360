import { Component, OnInit } from '@angular/core';
import { AbstractControl, UntypedFormBuilder, UntypedFormGroup, Validators } from "@angular/forms";
import { ActivatedRoute, Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import { GeneralService } from 'src/app/services/general.service';

@Component({
  selector: 'app-volunteer',
  templateUrl: './volunteer.component.html',
  styleUrls: ['./volunteer.component.scss']
})
export class VolunteerComponent implements OnInit {
  id: string = '';
  isSubmitted = false;
  volunteerForm: UntypedFormGroup = new UntypedFormGroup({});

  constructor(
    private formBuilder: UntypedFormBuilder,
    private appSettingsService: AppSettingsService,
    private apiService: AppSettingsService,
    private spinner: NgxSpinnerService,
    private generalService: GeneralService,
    private toastr: ToastrService,
    private router: Router,
    private activeRoute: ActivatedRoute,
  ) { }

  ngOnInit(): void {
    this.id = this.activeRoute.snapshot.params['id'];
    this.generateFormObj();
  }

  generateFormObj() {
    this.volunteerForm = this.formBuilder.group({
      project_id: [this.id],
      volunteer_type: ["", Validators.required],
      volunteer_comment: ["", Validators.required],
    });
  }

  get f(): { [key: string]: AbstractControl } {
    return this.volunteerForm.controls;
  }

  onSubmit(): void {
    this.isSubmitted = true;
    if (this.volunteerForm.invalid) {
      return;
    }
    this.spinner.show();
    this.appSettingsService.addVolunteer(this.volunteerForm.value).subscribe((res: any) => {
      if (res) {
        this.spinner.hide();
        if (res.success == true) {
          this.toastr.success(res.message, "Success");
          this.router.navigate(['project/details', this.id]);
        } else {
          this.generalService.getErrorMsg(res.message);
        }
      }
      this.spinner.hide();
    });
  }

}
