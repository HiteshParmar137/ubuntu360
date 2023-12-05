import { Component, OnInit, ElementRef, Renderer2, ViewChild, AfterViewInit  } from '@angular/core';
import { FormArray, UntypedFormBuilder, UntypedFormGroup, Validators, FormGroup, FormBuilder, AbstractControl, ValidationErrors, ValidatorFn, NgForm  } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import { GeneralService } from 'src/app/services/general.service';
import Swal from 'sweetalert2';
import { Lightbox } from 'ngx-lightbox';
import { Location } from '@angular/common';
import { IMultiSelectOption,IMultiSelectSettings,IMultiSelectTexts } from 'ngx-bootstrap-multiselect';
import * as $ from 'jquery';
import 'select2';
@Component({
  selector: 'app-project-edit',
  templateUrl: './project-edit.component.html',
  styleUrls: ['./project-edit.component.scss']
})
export class ProjectEditComponent implements OnInit, AfterViewInit {
  id: any = '';
  projectBasicDetails: UntypedFormGroup = new UntypedFormGroup({});
  projectMediaDetails: UntypedFormGroup = new UntypedFormGroup({});
  project_basic_step = false;
  project_media_step = false;
  step = 1;
  countries: [] = [];
  sdgs: [] = [];
  categories: any = [];
  formData = new FormData();
  projectId: string = '';
  projectDetails: any = [];
  categoryOptions: any = [];
  projectOptionOptions: any = [];
  productForm: FormGroup;
  coverImage: string='';
  images: any[] = [];
  imageAlbum: any[] = [];
  videos: any[] = [];
  videoAlbum: any[] = [];
  documents: any[] = [];
  documentAlbum: any[] = [];
  coverImageValid: any;
  imageFileValid: any;
  coverFileValid: any;
  videoFileValid: any;
  docFileValid: any;
  fileArrayType: any;
  fileImgTypeSupport: string = '';
  fileVidTypeSupport: string = '';
  fileDocTypeSupport: string = '';
  project_donation_type_array:any=[];
  myProjectTypeOptions: IMultiSelectOption[]=[];
  mySettings: IMultiSelectSettings = {
    enableSearch: false,
    checkedStyle: 'fontawesome',
    buttonClasses: 'btn btn-default btn-block',
    dynamicTitleMaxItems: 3,
    displayAllSelectedText: true
};
// Text configuration
myTexts: IMultiSelectTexts = {
  checkAll: 'Select all',
  uncheckAll: 'Unselect all',
  checked: 'item selected',
  checkedPlural: 'items selected',
  searchPlaceholder: 'Find',
  searchEmptyResult: 'Nothing found...',
  searchNoRenderText: 'Type in search box to see results...',
  defaultTitle: 'Please select project type',
  allSelected: 'All selected',
};
sdg_ids_array:any=[];
mySdgOptions: IMultiSelectOption[]=[];
mySdgSettings: IMultiSelectSettings = {
  enableSearch: false,
  checkedStyle: 'fontawesome',
  buttonClasses: 'btn btn-default btn-block',
  dynamicTitleMaxItems: 3,
  displayAllSelectedText: true
};
// Text configuration
mySdgTexts: IMultiSelectTexts = {
  checkAll: 'Select all',
  uncheckAll: 'Unselect all',
  checked: 'item selected',
  checkedPlural: 'items selected',
  searchPlaceholder: 'Find',
  searchEmptyResult: 'Nothing found...',
  searchNoRenderText: 'Type in search box to see results...',
  defaultTitle: 'Please choose one or more SDG',
  allSelected: 'All selected',
};
countryName:string = '';
@ViewChild('form') form!: NgForm;
@ViewChild('countryList') countryListRef!: ElementRef;
  constructor(
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private formBuilder: UntypedFormBuilder,
    private route: ActivatedRoute,
    public router: Router,
    public generalService: GeneralService,
    private appSettingsService: AppSettingsService,
    private fb: FormBuilder,
    private _lightbox: Lightbox,
    private location: Location,
    private elementRef: ElementRef, private renderer: Renderer2
  ) {

    this.route.paramMap.subscribe((params) => {
      if (params.get("id")) {
        this.id = params.get("id");
      }
    });
    this.productForm = this.fb.group({
      name: '',
      quantities: this.fb.array([]),
    });
  }
  options = [
    "France",
    "United Kingdom",
    "Germany",
    "Belgium",
    "Netherlands",
    "Spain",
    "Italy",
    "Poland",
    "Austria"
  ]
  
  ngOnInit(): void {
    this.projectDetails.country='';
    this.projectDetails.project_donation_type='';
    this.projectDetails.sdg_ids='';
    this.projectDetails.category_id='';
    this.getProjectCommonList();
    this.generateFormObj();
    this.myProjectTypeOptions = [
      { id: 1, name: 'Donation' },
      { id: 2, name: 'Volunteer' },
    ];
  
  }
  ngAfterViewInit() {
    // $(this.countryListRef.nativeElement).select2();
    // $(this.countryListRef.nativeElement).on('change', () => {
    //   this.projectBasicDetails.controls['country'].setValue($(this.countryListRef.nativeElement).val());
    // });
  }
  // forkJoin([projectDetails, characterHomeworld]).subscribe(results => {
  //   // results[0] is our character
  //   // results[1] is our character homeworld
  //   results[0].homeworld = results[1];
  //   this.loadedCharacter = results[0];
  // });
  getProjectCommonList() {
    this.appSettingsService.getProjectCommonList().subscribe((res: any) => {
      if (res[0].success == true && res[1].success == true && res[2].success == true) {
        this.categories = res[0].data;
        this.countries = res[1].data;
        this.sdgs = res[2].data;
        this.mySdgOptions = res[2].data;
        if (this.id) {
          this.getProjectDetails();
        } else {
          this.generateFormObj();
        }
      }
    });
  }
  getProjectDetails() {
    this.appSettingsService.getFrontUserProjectDetails(this.id).subscribe((res: any) => {
      if (res[0].success == true) {
        this.projectDetails = res[0].data;
        if(this.projectDetails.default_image_name){
          this.coverImage=this.projectDetails.default_image;
        }

        if (this.projectDetails.project_donation_type.includes(1)) {
          this.isDonation = true;
        } else {
          this.isDonation = false;
        }

        if (this.projectDetails.project_donation_type.includes(2)) {
          this.isVolunteer = true;
        } else {
          this.isVolunteer = false;
        }

        this.getCountryName(this.projectDetails.country);
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

    });
  }
  generateFormObj() {
    // this.projectBasicDetails = this.formBuilder.group({
    //   id: [this.id],
    //   project_type: ['', Validators.required],
    //   title: ['', Validators.required],
    //   description: ['', Validators.required],
    //   city: ['', Validators.required],
    //   country: [''],
    //   sdg_id: ['', Validators.required],
    //   category: ['', Validators.required],
    // });
    if(this.projectDetails.project_donation_type){
      this.project_donation_type_array =this.projectDetails.project_donation_type
    }else{
      this.project_donation_type_array=[];
    }
    if(this.projectDetails.sdg_ids){
      this.sdg_ids_array =this.projectDetails.sdg_ids
    }else{
      this.sdg_ids_array=[];
    }
    this.projectBasicDetails = this.formBuilder.group({
      id: [this.id],
      project_type: [this.projectDetails.project_type, Validators.required],
      title: [this.projectDetails.title, [Validators.required, Validators.maxLength(250)]],
      amount: [this.projectDetails.amount, [Validators.pattern(/^\d+(\.\d{1,2})?$/), this.priceLimitValidator(1000000000)]],
      volunteer: [this.projectDetails.volunteer, [ Validators.pattern(/^\d+$/), this.priceLimitValidator(10000)]],
      
      project_donation_type: [this.project_donation_type_array, [ Validators.required]],
      category_id: [this.projectDetails.category_id, Validators.required],
      description: [this.projectDetails.description, [Validators.required, Validators.maxLength(500)]],
      city: [this.projectDetails.city, [Validators.required, Validators.maxLength(50)]],
      country: [this.projectDetails.country, Validators.required],
      project_url: [this.projectDetails.project_url, Validators.required],
      sdg_ids: [this.sdg_ids_array, Validators.required],
      
    });
    // if(this.projectDetails.country){
    //   const selectedElement = this.renderer.selectRootElement('#country');
    //   $(selectedElement.nativeElement).select2();
    // }
    

    
    // if (this.id) {
    //   var projectOption_ids: FormArray = this.projectBasicDetails.get('project_donation_type') as FormArray;
  
    //   for (let projectOption of this.categories) {
    //     var isSdgExist = false;
    //     for (let projectprojectOption of this.projectDetails.projectOption_ids) {
    //       if (projectOption.id == projectprojectOption.projectOption_id) {
    //         isSdgExist = true;
    //       }
    //     }
    //     if (isSdgExist == true) {
    //       projectOption_ids.push(new FormControl(projectOption.id));
    //       projectOption.selected = true;
    //       this.projectOptionOptions.push(projectOption);
    //     } else {
    //       projectOption.selected = false;
    //       this.projectOptionOptions.push(projectOption);
    //     }
    //   }
    // }

    this.projectMediaDetails = this.formBuilder.group({
      default_image: [this.projectDetails.default_image, Validators.required]
    });
  }
  get projectbasic() { return this.projectBasicDetails.controls; }

  get projectMedia() { return this.projectMediaDetails.controls; }

  // onCheckboxChange(e: any) {
  //   const category_ids: FormArray = this.projectBasicDetails.get('category_ids') as FormArray;
  //   if (e.target.checked) {
  //     category_ids.push(new FormControl(e.target.value));
  //     for (let categoryOptionKey in this.categoryOptions) {
  //       if (this.categoryOptions[categoryOptionKey]['id'] == e.target.value) {
  //         this.categoryOptions[categoryOptionKey]['selected'] = true;
  //       }
  //     }
  //   } else {
  //     let i: number = 0;
  //     category_ids.controls.forEach((item: any) => {
  //       if (item.value == e.target.value) {
  //         for (let categoryOptionKey in this.categoryOptions) {
  //           if (this.categoryOptions[categoryOptionKey]['id'] == item.value) {
  //             this.categoryOptions[categoryOptionKey]['selected'] = false;
  //           }
  //         }
  //         category_ids.removeAt(i);
  //         return;
  //       }
  //       i++;
  //     });
  //   }
  // }
  quantities(): FormArray {
    return this.productForm.get("quantities") as FormArray
  }

  newQuantity(): FormGroup {
    return this.fb.group({
      qty: '',
      price: '',
    })
  }

  addQuantity() {
    this.quantities().push(this.newQuantity());
  }

  removeQuantity(i: number) {
    this.quantities().removeAt(i);
  }
  next() {
    
    if (this.step == 1) {
      this.project_basic_step = true;
      const volunteerControl = this.projectBasicDetails.get('volunteer');
        if (this.isVolunteer == true && volunteerControl != null) {
          volunteerControl.setValidators([Validators.required]);
        } else {
          volunteerControl?.clearValidators();
          volunteerControl?.setValue('');
        }    
        volunteerControl?.updateValueAndValidity();
      
        const donationControl = this.projectBasicDetails.get('amount');
        if (this.isDonation == true && donationControl != null) {
          donationControl.setValidators([Validators.required]);
        } else {
          donationControl?.clearValidators();
          donationControl?.setValue('');
        }    
        donationControl?.updateValueAndValidity();

      if (this.projectBasicDetails.invalid) { this.scrollToFirstInvalidControl(); return }
      this.mergeArray(this.projectBasicDetails.value);
      this.saveData(this.formData, 'next');
    }
    else if (this.step == 2) {
      // if(this.coverImage){
      //   this.step++;
      // }else{
      //   this.coverImageValid=false;
      // }
      this.step++;
      this.getProjectDetails();
    }
  }

  previous() {
    this.step--
    if (this.step == 1) {
      this.project_media_step = false;
    }
  }

  saveData(formValue: any, submitType: string) {
    this.appSettingsService.saveProjectDetails(formValue, this.step, submitType).subscribe((res: any) => {
      if (res) {
        if (res.success == true && res.data != null) {
          if (this.step == 1) {
            this.step++;
          }
          this.projectId = res.data.project_id;
          this.id = res.data.project_id;
          this.projectBasicDetails.controls['id'].setValue(this.id );
          this.location.replaceState('/user/projects/edit/' + res.data.project_id);
          this.getProjectDetails();
          if (submitType && submitType == 'submit') {
            this.toastr.success(res.message, "success");
            this.router.navigate(['/user/projects/list']);
          }
          
        } else {
          this.generalService.getErrorMsg(res.message);
        }
      }
    },
    (error) => {             
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

  submit() {
    // this.project_media_step = true;
    // if (this.projectMediaDetails.invalid) { return }
    // this.mergeArray(this.projectMediaDetails.value);
    // alert("Well done!!");
    this.mergeArray(this.projectBasicDetails.value);
    this.saveData(this.formData, 'submit');
  }
  addImage(type: string) {
    if (type == "image") {
      this.images.push({
        image: ''
      });
    }
    if (type == "video") {
      this.videos.push({
        video: ''
      });
    }
    if (type == "document") {
      this.documents.push({
        document: ''
      });
    }
  }
  removeCoverImage(id:any){
    let deleteArray = { 'title': 'Are you sure?', 'text': 'You want remove this cover image?', 'data': id, 'api_name': 'project_default_image' };
    this.generalService.openDeleteConfirmationDialog(deleteArray).then(response => {
      if (response) {
        this.appSettingsService.removeCoverImage(id).subscribe((res: any) => {
          if (res) {
            if (res.success == true) {
              Swal.fire('Removed!', res.message, 'success');
              this.coverImage='';
              //this.coverImageValid=false;
              this.getProjectDetails();
            } else {
              Swal.fire('Error!', res.message, 'error');
            }
          }
        },
        (error) => {             
          this.generalService.getErrorMsg(error.error.message);
        });
      }
    });
  }
  removeImage(doc_id: string, i: number, type: string) {
    let text = '';
    if (type == "image") {
      text = 'cover image';
    } else if (type == "video") {
      text = 'video';
    } else if (type == 'document') {
      text = 'document';
    }
    let deleteArray = { 'title': 'Are you sure?', 'text': 'You want remove this '+text+'?', 'data': doc_id, 'api_name': 'project_document' };
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
          this.generalService.getErrorMsg(error.error.message);
        });
      }
    });
  }
  @ViewChild('coverInput') coverInput: any;
  uploadCoverImage(event: any, type: string){
    if (event.target.files && event.target.files[0]) {
      let file = event.target.files[0];
      const fileArray = {
        'cover_image': ['image/jpeg', 'image/png']
      };
      this.fileImgTypeSupport = fileArray.cover_image.join(',');

      if (type == 'cover_image') {
        this.fileArrayType = fileArray.cover_image;
      }
      if ($.inArray(file.type, this.fileArrayType) !== -1) {
        if (type == 'cover_image') {
          this.coverFileValid = true;
        }
        const formData = new FormData();
        formData.append('document', event.target.files[0]);
        formData.append('type', type);
        formData.append('project_id', this.id);
        this.appSettingsService.uploadProjectDoc(formData).subscribe((res: any) => {
          if (res) {
            if (res.success == true) {
              this.toastr.success(res.message, "Success");
              this.coverImageValid=true;
              if (res.data.type == 'cover_image') {
                this.coverImage=res.data.document;
                this.coverInput.nativeElement.value = '';
              }
              this.getProjectDetails();
              //this.router.navigate(['/dashboard']);
            } else {
              this.generalService.getErrorMsg(res.message);
            }
          }
        },
        (error) => {             
          this.generalService.getErrorMsg(error.error.message);
        });
      } else {
        if (type == 'cover_image') {
          this.coverFileValid = false;
        }
      }
    }
  }
  @ViewChild('imageInput') imageInput: any;
  @ViewChild('videoInput') videoInput: any;
  @ViewChild('documentInput') documentInput: any;
  uploadDocument(event: any, type: string) {
    if (event.target.files && event.target.files[0]) {
      let file = event.target.files[0];
      
      const fileArray = {
        'image': ['image/jpeg', 'image/png'], 'video': ['video/mp4', 'video/flv', 'video/wmv'], 'document': ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'application/pdf', 'application/msword']
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
            if (res.success == true) {
              this.toastr.success(res.message, "Success");
              if (res.data.type == 'image') {
                this.imageInput.nativeElement.value = '';
                this.images.push({ 'id': res.data.doc_id, 'document': res.data.document });
              }
              if (res.data.type == 'video') {
                this.videoInput.nativeElement.value = '';
                this.videos.push({ 'id': res.data.doc_id, 'document': res.data.document });
              }
              if (res.data.type == 'document') {
                this.documentInput.nativeElement.value = '';
                this.documents.push({ 'id': res.data.doc_id, 'document': res.data.document, 'document_name': res.data.document_name });
              }
            } else {
              this.generalService.getErrorMsg(res.message);
            }
          }
        },
        (error) => {             
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

  open(index: number, type: string): void {
    // open lightbox
    if (type == 'image') {
      this._lightbox.open(this.imageAlbum, index);
    }
    if (type == 'video') {
      this._lightbox.open(this.videoAlbum, index);
    }
    if (type == 'document') {
      this._lightbox.open(this.documentAlbum, index);
    }
  }

  close(): void {
    // close lightbox programmatically
    this._lightbox.close();
  }

  priceLimitValidator(limit: number): ValidatorFn {
    return (control: AbstractControl): ValidationErrors | null => {
      const price = parseFloat(control.value);
      if (isNaN(price) || price < 1 || price > limit) {
        return { priceLimit: true };
      }
      return null;
    };
  }
  scrollToFirstInvalidControl() {
    const firstInvalidControl = this.elementRef.nativeElement.querySelector('.ng-invalid');
    if (firstInvalidControl) {
      firstInvalidControl.scrollIntoView({ behavior: 'smooth' });
    }
  }
  getCountryName(c_id:any) {
    this.appSettingsService.getProjectCommonList().subscribe((res) => {      
      if (res.length > 1 && res[1] != 'undefined' && res[1].success === true) {
        let countryList = res[1].data;
        let countryObj = countryList.filter((item:any) => {
          return item.id == c_id;
        })
        if (countryObj.length > 0){
          this.countryName = countryObj[0].name;
        }
      }
    });
  }
  
  inputValue:string = '';
  capitalizeFirstLetter(event:any) {
    this.inputValue = event.target.value;
    if (this.inputValue != '') {
      event.target.value = this.inputValue.charAt(0).toUpperCase() + this.inputValue.slice(1);
    } else {
      event.target.value = '';
    }
  }

  isVolunteer:boolean = false;
  isDonation:boolean = false;
  hideShowField(event:any) {
    const type = this.projectBasicDetails.value.project_donation_type;
    if (type.includes(1)) {
      this.isDonation = true;
    } else {
      this.isDonation = false;
    }

    if (type.includes(2)) {
      this.isVolunteer = true;
    } else {
      this.isVolunteer = false;
    }
  }
}
