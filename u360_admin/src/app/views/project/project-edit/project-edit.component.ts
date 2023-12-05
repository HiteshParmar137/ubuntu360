import { Component, OnInit } from '@angular/core';
import { AbstractControl, FormArray, FormControl, UntypedFormBuilder, UntypedFormGroup, Validators, FormGroup, FormBuilder } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import { GeneralService } from 'src/app/services/general.service';
import { Location } from '@angular/common';
import Swal from 'sweetalert2';
@Component({
  selector: 'app-project-edit',
  templateUrl: './project-edit.component.html',
  styleUrls: ['./project-edit.component.scss']
})
export class ProjectEditComponent implements OnInit {
  projectBasicDetails: UntypedFormGroup = new UntypedFormGroup({});
  projectMediaDetails: UntypedFormGroup = new UntypedFormGroup({});
  project_basic_step = false;
  project_media_step = false;
  step = 1;
  countries: [] = [];
  sdgs: [] = [];
  categories: any = [];
  id: any = '';
  projectId: string = '';
  projectDetails: any = [];
  categoryOptions: any = [];
  images: any[] = [];
  imageAlbum: any[] = [];
  videos: any[] = [];
  videoAlbum: any[] = [];
  documents: any[] = [];
  documentAlbum: any[] = [];
  formData = new FormData();
  imageFileValid: any;
  videoFileValid: any;
  docFileValid: any;
  fileArrayType: any;
  fileImgTypeSupport: string = '';
  fileVidTypeSupport: string = '';
  fileDocTypeSupport: string = '';

  constructor(
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private formBuilder: UntypedFormBuilder,
    private route: ActivatedRoute,
    public router: Router,
    public generalService: GeneralService,
    private fb: FormBuilder,
    private appSettingsService: AppSettingsService,
    private location: Location,
  ) {
    this.route.paramMap.subscribe((params) => {
      if (params.get("id")) {
        this.id = params.get("id");
      }
    });
  }

  generateFormObj() {
    this.projectBasicDetails = this.formBuilder.group({
      id: [this.id],
      project_type: [this.projectDetails.project_type, Validators.required],
      title: [this.projectDetails.title, Validators.required],
      description: [this.projectDetails.description, Validators.required],
      city: [this.projectDetails.city, Validators.required],
      country: [this.projectDetails.country, Validators.required],
      sdg_id: [this.projectDetails.sdg_id, Validators.required],
      category_ids: this.formBuilder.array([], [Validators.required]),
    });

    if (this.id) {
      var category_ids: FormArray = this.projectBasicDetails.get('category_ids') as FormArray;

      for (let category of this.categories) {
        var isSdgExist = false;
        for (let projectCategory of this.projectDetails.category_ids) {
          if (category.id == projectCategory.category_id) {
            isSdgExist = true;
          }
        }
        if (isSdgExist == true) {
          category_ids.push(new FormControl(category.id));
          category.selected = true;
          this.categoryOptions.push(category);
        } else {
          category.selected = false;
          this.categoryOptions.push(category);
        }
      }
    }

    this.projectMediaDetails = this.formBuilder.group({
      image: ['', Validators.required],
      university: ['', Validators.required],
      total_marks: ['', Validators.required]
    });
  }

  ngOnInit(): void {
    this.getProjectCommonList();
    this.generateFormObj();
  }

  getProjectDetails() {
    this.appSettingsService.getProjectDetails(this.id).subscribe((res: any) => {
      if (res[0].success == true) {
        this.projectDetails = res[0].data;
      }
      if (res[1].success == true) {
        this.images = res[1].data;
      }
      if (res[2].success == true) {
        this.videos = res[2].data;
      }
      if (res[3].success == true) {
        this.documents = res[3].data;
      }
      //var i;
      for (var i = 0; i < this.images.length; i++) {
        const album = {
          src: this.images[i]['document'],
          caption: '',
          thumb: ''
        };
        this.imageAlbum.push(album);
      }
      for (var i = 0; i < this.videos.length; i++) {
        const album = {
          src: this.videos[i]['document'],
          caption: '',
          thumb: ''
        };
        this.videoAlbum.push(album);
      }
      for (var i = 0; i < this.documents.length; i++) {
        const album = {
          src: this.documents[i]['document'],
          caption: '',
          thumb: ''
        };
        this.documentAlbum.push(album);
      }
      this.generateFormObj();
    },
    (error) => {     
      this.spinner.hide();        
      this.generalService.getErrorMsg(error.error.message);
    });
  }

  next() {
    if (this.step == 1) {
      this.project_basic_step = true;
      if (this.projectBasicDetails.invalid) { return }
      this.mergeArray(this.projectBasicDetails.value);
      this.saveData(this.formData, 'next');
      this.step++;
    }
  }

  previous() {
    this.step--
    if (this.step == 1) {
      this.project_media_step = false;
    }
  }

  get projectbasic() { return this.projectBasicDetails.controls; }

  get projectMedia() { return this.projectMediaDetails.controls; }

  getProjectCommonList() {
    this.appSettingsService.getProjectCommonList().subscribe((res: any) => {
      if (res[0].success == true && res[1].success == true && res[2].success == true) {
        this.categories = res[0].data;
        this.countries = res[1].data;
        this.sdgs = res[2].data;
        if (this.id) {
          this.getProjectDetails();
        } else {
          this.generateFormObj();
        }
      }
    },
    (error) => {     
      this.spinner.hide();        
      this.generalService.getErrorMsg(error.error.message);
    });
  }

  saveData(formValue: any, submitType: string) {
    this.appSettingsService.saveProjectDetails(formValue, this.step, submitType).subscribe((res: any) => {
      if (res) {
        if (res.success == true && res.data != null) {
          this.spinner.hide();
          this.toastr.success(res.message, "success");
          this.projectId = res.data.project_id;
          this.id = res.data.project_id;
          this.location.replaceState('/project/edit/' + res.data.project_id);
          this.getProjectDetails();
        } else {
          this.generalService.getErrorMsg(res.message);
          this.spinner.hide();
        }
      }
    },
    (error) => {     
      this.spinner.hide();        
      this.generalService.getErrorMsg(error.error.message);
    });
  }

  mergeArray(formArray: any) {
    Object.keys(formArray).forEach(key => {
      if (this.formData.has(key) == false) {
        this.formData.append(key, formArray[key]);
      } else {
        this.formData.set(key, formArray[key]);
      }
    })
  }

  onCheckboxChange(e: any) {
    const category_ids: FormArray = this.projectBasicDetails.get('category_ids') as FormArray;
    if (e.target.checked) {
      category_ids.push(new FormControl(e.target.value));
      for (let categoryOptionKey in this.categoryOptions) {
        if (this.categoryOptions[categoryOptionKey]['id'] == e.target.value) {
          this.categoryOptions[categoryOptionKey]['selected'] = true;
        }
      }
    } else {
      let i: number = 0;
      category_ids.controls.forEach((item: any) => {
        if (item.value == e.target.value) {
          for (let categoryOptionKey in this.categoryOptions) {
            if (this.categoryOptions[categoryOptionKey]['id'] == item.value) {
              this.categoryOptions[categoryOptionKey]['selected'] = false;
            }
          }
          category_ids.removeAt(i);
          return;
        }
        i++;
      });
    }
  }

  uploadDocument(event: any, type: string) {
    if (event.target.files && event.target.files[0]) {
      let file = event.target.files[0];
      const fileArray = {
        'image': ['image/jpeg', 'image/png'], 'video': ['video/mp4', 'video/flv', 'video/wmv'], 'document': ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'application/pdf','application/msword']
      };
      this.fileImgTypeSupport = fileArray.image.join(',');
      this.fileVidTypeSupport = fileArray.video.join(',');
      this.fileDocTypeSupport = fileArray.document.join(',');
      
      if (type == 'image') {
        this.fileArrayType = fileArray.image;
      }
      if (type == 'video') {
        this.fileArrayType = fileArray.video;
      }
      if (type == 'document') {
        this.fileArrayType = fileArray.document;
      }
      if ($.inArray(file.type, this.fileArrayType) !== -1) {
        if (type == 'image') {
          this.imageFileValid = true;
        } else if (type == 'video') {
          this.videoFileValid = true;
        } else if (type == 'document') {
          this.docFileValid = true;
        }
        const formData = new FormData();
        formData.append('document', event.target.files[0]);
        formData.append('type', type);
        formData.append('project_id', this.id);
        this.appSettingsService.uploadProjectDoc(formData).subscribe((res: any) => {
          if (res) {
            this.spinner.hide();
            if (res.success == true && res.data != null) {
              this.toastr.success(res.message, "Success");
              if (res.data.type == 'image') {
                this.images.push({ 'id': res.data.doc_id, 'document': res.data.document });
              }
              if (res.data.type == 'video') {
                this.videos.push({ 'id': res.data.doc_id, 'document': res.data.document });
              }
              if (res.data.type == 'document') {
                this.documents.push({ 'id': res.data.doc_id, 'document': res.data.document });
              }
              //this.router.navigate(['/dashboard']);
            } else {
              this.generalService.getErrorMsg(res.message);
            }
          }
          this.spinner.hide();
        },
        (error) => {     
          this.spinner.hide();        
          this.generalService.getErrorMsg(error.error.message);
        });
      } else {
        if (type == 'image') {
          this.imageFileValid = false;
        } else if (type == 'video') {
          this.videoFileValid = false;
        } else if (type == 'document') {
          this.docFileValid = false;
        }
      }

    }
  }

  removeImage(doc_id: string, i: number, type: string) {
    let deleteArray = { 'title': 'Are you sure?', 'text': 'You want remove this document?', 'data': doc_id, 'api_name': 'project_document' };
    this.generalService.openDeleteConfirmationDialog(deleteArray).then(response => {
      if (response) {
        this.appSettingsService.projectDocumentDelete(doc_id).subscribe((res: any) => {
          if (res) {
            if (res.success == true) {
              Swal.fire('Removed!', res.message, 'success');
              if (type == 'image') {
                this.images.splice(i, 1);
              }
              if (type == 'video') {
                this.videos.splice(i, 1);
              }
              if (type == 'document') {
                this.documents.splice(i, 1);
              }
            } else {
              Swal.fire('Error!', res.message, 'error');
            }
          }
        },
        (error) => {     
          this.spinner.hide();        
          this.generalService.getErrorMsg(error.error.message);
        });
      }
    });
  }

  submit() {
    if (this.step == 2) {
      this.project_media_step = true;
      if (this.projectMediaDetails.invalid) { return }
      this.mergeArray(this.projectMediaDetails.value);
      this.saveData(this.formData, 'submit');
      alert("Well done!!");
    }
  }
}
