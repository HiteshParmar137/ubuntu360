import { Component, OnInit } from '@angular/core';
import { AbstractControl, UntypedFormBuilder, UntypedFormGroup, Validators } from "@angular/forms";
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import { GeneralService } from 'src/app/services/general.service';
import { Location } from '@angular/common';

@Component({
  selector: 'app-project-add',
  templateUrl: './project-add.component.html',
  styleUrls: ['./project-add.component.scss']
})
export class ProjectAddComponent implements OnInit {

  projectBasicDetails: UntypedFormGroup = new UntypedFormGroup({});;
  projectMediaDetails: UntypedFormGroup = new UntypedFormGroup({});;
  project_basic_step = false;
  project_media_step = false;
  step = 1;
  countries: [] = [];
  sdgs: [] = [];
  categories: [] = [];
  formData = new FormData();
  projectId: number = 0;

  constructor(
    private formBuilder: UntypedFormBuilder,
    private appSettingsService: AppSettingsService,
    private apiService: AppSettingsService,
    private spinner: NgxSpinnerService,
    private generalService: GeneralService,
    private toastr: ToastrService,
    private location: Location
  ) { }

  ngOnInit(): void {
    this.projectBasicDetails = this.formBuilder.group({
      id: [''],
      project_type: ['', Validators.required],
      title: ['', Validators.required],
      description: ['', Validators.required],
      city: ['', Validators.required],
      country: [''],
      sdg_id: ['', Validators.required],
      category: ['', Validators.required],
    });

    this.projectMediaDetails = this.formBuilder.group({
      highest_qualification: ['', Validators.required],
      university: ['', Validators.required],
      total_marks: ['', Validators.required]
    });
    this.getCountries();
    this.getSdgs();
    this.getCategories();
  }

  get projectbasic() { return this.projectBasicDetails.controls; }

  get projectMedia() { return this.projectMediaDetails.controls; }

  next() {
    if (this.step == 1) {
      this.project_basic_step = true;
      if (this.projectBasicDetails.invalid) { return }
      this.mergeArray(this.projectBasicDetails.value);
      this.saveData(this.formData, 'next');
      this.step++
    }
  }

  previous() {
    this.step--
    if (this.step == 1) {
      this.project_media_step = false;
    }
  }

  getCountries() {
    this.appSettingsService.getCountriesList().subscribe((res: any) => {
      if (res.success) {
        this.countries = res.data;
      }
    });
  }

  getSdgs() {
    this.appSettingsService.getSdgsList().subscribe((res: any) => {
      if (res.success) {
        this.sdgs = res.data;
      }
    });
  }

  getCategories() {
    this.appSettingsService.getCategoriesList().subscribe((res: any) => {
      if (res.success) {
        this.categories = res.data;
      }
    });
  }

  saveData(formValue: any, submitType: string) {
    this.apiService.saveProjectDetails(formValue, this.step, submitType).subscribe((res: any) => {
      if (res) {
        if (res.success && res.data != null) {
          this.spinner.hide();
          this.toastr.success(res.message, "success");
          this.projectId = res.data.project_id;
          this.location.replaceState('/projects/edit/'+res.data.project_id);
        } else {
          this.generalService.getErrorMsg(res.message);
          this.spinner.hide();
        }
      }
    });
  }

  mergeArray(formArray: any) {
    Object.keys(formArray).forEach(key => {
      if (this.formData.has(key) === false) {
        this.formData.append(key, formArray[key]);
      } else {
        this.formData.set(key, formArray[key]);
      }
    })
  }

  submit() {
    if (this.step == 2) {
      this.project_media_step = true;
      if (this.projectMediaDetails.invalid) { return }
      this.mergeArray(this.projectMediaDetails.value);
      this.saveData(this.formData, 'submit');
      alert("Well done!!")
    }
  }

}
