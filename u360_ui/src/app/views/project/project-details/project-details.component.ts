import { Component, OnInit, Input, Renderer2 } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import { AuthService } from '../../front-user/auth/auth.service';
import { GeneralFunction } from 'src/app/_directives/general-function.directive';
import { OwlOptions } from 'ngx-owl-carousel-o';
import { GeneralService } from 'src/app/services/general.service';
import { UntypedFormBuilder, UntypedFormGroup, Validators, AbstractControl, ValidationErrors, ValidatorFn } from '@angular/forms';
import * as $ from 'jquery';
import 'bootstrap';
import 'bootstrap-datepicker';
import 'node_modules/bootstrap-datepicker/dist/css/bootstrap-datepicker.css';
import 'node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js'
@Component({
  selector: 'app-project-details',
  templateUrl: './project-details.component.html',
  styleUrls: ['./project-details.component.scss']
})
export class ProjectDetailsComponent implements OnInit {

  id: string = '';
  projectDetails: any = [];
  finalResponse: [] = [];
  finalResponseCommunity: any = [];
  topDoners: any = [];
  responseFlag: boolean = false;
  noDataFlag: string = "";
  page: number = 1;
  total: number = 0;
  per_page: number = 0;
  current_page: any = 1;
  limit: any;
  offset: any;
  last_page: any = '';
  perPageRecord = [];
  pager: any = {};
  index: number = 0;
  start_record: any;
  project_documents: any = {};
  isLoggedIn: boolean = false;
  projectList: any = [];
  donationPurposeForm: UntypedFormGroup = new UntypedFormGroup({});
  volunteerForm: UntypedFormGroup = new UntypedFormGroup({});
  oneTimeDonationForm: UntypedFormGroup = new UntypedFormGroup({});
  recuringDonationForm: UntypedFormGroup = new UntypedFormGroup({});
  communityForm: UntypedFormGroup = new UntypedFormGroup({});
  commentForm: UntypedFormGroup = new UntypedFormGroup({});
  isDonationTypeAvailable: boolean = false;
  isVolunteerTypeAvailable: boolean = false;
  donationTypeFormSubmit: boolean = false;
  donationFormSubmit: boolean = false;
  volunteerFormSubmit: boolean = false;
  isDonationForm: boolean = false;
  isDonationTypeForm: boolean = true;
  isVolunteerForm: boolean = false;
  isVolunteerFormSuccess: boolean = false;
  isDonationFormSuccess: boolean = false;
  totalAmount: any;
  isDispalayDatepicker: boolean = false;
  // isOntimeDonation:boolean=true;
  // isRecuringDonation:boolean=false;
  recuringImageFileValid: boolean = false;
  oneTimeImageFileValid: boolean = false;
  fileArrayType: any;
  fileImgTypeSupport: string = '';
  formData = new FormData();
  recuringFormData = new FormData();
  communityFormData = new FormData();
  recuringFileName: string = '';
  donationFileName: string = '';
  communityFileName: string = '';
  // community variable
  communityImageFileValid: boolean = false;
  profile_image: any = '';
  userId: any = '';
  userProfile: any = '';
  token:any = '';
  constructor(
    private activeRoute: ActivatedRoute,
    private appSettingsService: AppSettingsService,
    private spinner: NgxSpinnerService,
    private toastr: ToastrService,
    private authService: AuthService,
    private router: Router,
    public generalService: GeneralService,
    private formBuilder: UntypedFormBuilder,
    private renderer: Renderer2,
  ) { }
  customOptions: OwlOptions = {
    loop: false,
    mouseDrag: true,
    touchDrag: true,
    pullDrag: false,
    dots: true,
    nav: false,
    navSpeed: 600,
    autoHeight: true,
    // navText: ['&#8249', '&#8250;'],
    margin: 38,
    responsive: {
      0: {
        items: 1
      },
      400: {
        items: 2
      },
      760: {
        items: 2,
        margin: 20
      },
      1000: {
        items: 3
      }
    },
  }
  ngOnInit(): void {
    this.userId = localStorage.getItem('userId') || '';
    this.userProfile = localStorage.getItem('image') || '';
    this.id = this.activeRoute.snapshot.params['id'];
    this.token = localStorage.getItem("token");
    this.getProjectDetails();
    // this.getCommunityDetails();
    if (this.authService.isLoggedIn()) {
      this.isLoggedIn = true;
    }
    this.generateDonationPurposeFormObj();
    this.generateVolunteerFormObj();
    this.onTimeDonationFormObj();
    this.recuringDonationFormObj();
    this.generateCommunityFormObj();
    this.generateCommentFormObj();
    this.profile_image = localStorage.getItem('image');

    $('.expire_date').datepicker({
      format: 'mm/yyyy',
      autoclose: true,
      minViewMode: 'months',
      startDate: 'y',
      endDate: '+10y',
    }).on('changeDate', (e) => {
      // Get the selected date
      let selectedDate = e.format('mm/yyyy');

      // Update the form control value with the selected date
      this.oneTimeDonationForm.controls['expire_date'].setValue(selectedDate);
      this.recuringDonationForm.controls['expire_date'].setValue(selectedDate);
    });
    $('.recurring_date').datepicker({
      format: 'dd/mm/yyyy',
      autoclose: true,
      startDate: 'y',
      endDate: '+10y',
      beforeShowDay: function (date) {
        // Disable all dates with day set to 31
        if (date.getDate() === 31) {
          return {
            enabled: false
          };
        }
        return {
          enabled: true
        };
      }
    }).on('changeDate', (e) => {
      // Get the selected date
      let selectedDate = e.format('dd/mm/yyyy');

      // Update the form control value with the selected date
      this.recuringDonationForm.controls['recurring_date'].setValue(selectedDate);
    });
  }
  // community: starts
  generateCommunityFormObj() {
    this.communityForm = this.formBuilder.group({
      project_id: [this.id],
      comment: ['', [Validators.required, Validators.maxLength(500)]],
    });
  }

  getCommunityDetails(type: any = '') {
    const postData = { 'page': this.current_page, 'project_id': this.id };
    this.spinner.show();
    this.appSettingsService.getCommunityDetails(postData).subscribe((res) => {
      if (res.success === true) {
        this.responseFlag = true;
        if (res.data && res.data.response && res.data.response.length > 0) {
          this.index = (this.current_page - 1) * res.data.pagination.per_page + 1;
          this.limit = res.data.pagination.per_page;
          this.last_page = res.data.pagination.last_page;
          // this.finalResponseCommunity = [];
          let currentPaginationObject = {
            range: this.current_page,
            total: res.data.pagination.total,
          };
          this.pager = GeneralFunction.setPagination(
            currentPaginationObject,
            this.current_page,
            this.limit
          );
          // this.finalResponseCommunity = res.data.response;
          if (type == 'donation' || type == 'volunteer') {
            this.finalResponseCommunity = res.data.response;
          } else {
            this.finalResponseCommunity = this.finalResponseCommunity.concat(res.data.response);
          }

          this.topDoners = res.data.top_donation;
        } else {
          this.finalResponseCommunity = [];
          this.noDataFlag = "Data Not Found";
          this.last_page = res.data.pagination.last_page;
          this.current_page = res.data.pagination.current_page;
        }
      } else {
        this.finalResponseCommunity = [];
        this.noDataFlag = "Data Not Found";
      }
      this.spinner.hide();
    },
      (error) => {
        this.generalService.getErrorMsg(error.error.message);
      });
  }
  get communityControl() { return this.communityForm.controls; }

  uploadCommunityDocument(event: any, type: string = '') {
    let file = event.target.files[0];
    const fileArray = {
      'image_video': ['image/jpeg', 'image/png', 'video/mp4', 'video/flv', 'video/wmv']
    };
    this.fileImgTypeSupport = fileArray.image_video.join(',');
    this.fileArrayType = fileArray.image_video;
    if ($.inArray(file.type, this.fileArrayType) !== -1) {
      this.communityFormData.append('document', event.target.files[0]);
      this.communityFileName = file.name;
      this.communityImageFileValid = false;
    } else {
      this.communityImageFileValid = true;
    }
  }
  communityFormSubmit: boolean = false;
  addCommunity() {
    this.communityFormSubmit = true;
    // if(this.communityForm.value.donation_amount < 1){
    //   this.communityForm.controls['donation_amount'].setValue(0);
    // }
    // if(this.communityForm.value.tips_amount < 1){
    //   this.communityForm.controls['tips_amount'].setValue(0);
    // }
    if (this.communityForm.invalid) { return }

    this.mergeArray(this.communityForm.value, 'community');
    this.saveCommunity(this.communityFormData);
  }
  saveCommunity(formData: any) {
    this.communityFormSubmit = true;
    this.spinner.show();

    this.appSettingsService.saveCommunity(formData).subscribe((res: any) => {
      if (res) {
        this.spinner.hide();

        if (res.success) {
          this.toastr.success(res.message, "success");
          this.communityFormSubmit = false;
          this.finalResponseCommunity.unshift(res.data);
          this.communityForm.controls['comment'].setValue('');
          this.communityFileName = '';
        } else {
          this.generalService.getErrorMsg(res.message);
          this.spinner.hide();
          this.communityFormSubmit = false;
        }
      }
    },
      (error) => {
        this.generalService.getErrorMsg(error.error.message);
      });
  }

  likeUnlike(community_id: any, project_id: any, type: any, event: any, index: number) {
    const postData = { 'project_community_id': community_id, 'project_id': project_id, 'type': type };
    this.spinner.show();
    this.appSettingsService.likeUnlike(postData).subscribe((res: any) => {
      if (res) {
        if (res.success) {
          if (type == 'like') {
            // this.renderer.setProperty(event.target, 'textContent', 'Follow');
            this.renderer.removeClass(event.target, 'far');
            this.renderer.addClass(event.target, 'fas');
            this.finalResponseCommunity[index].like_count = res.data.total_like;
            this.finalResponseCommunity[index].is_like = 1;
          } else {
            this.renderer.removeClass(event.target, 'fas');
            this.renderer.addClass(event.target, 'far');
            this.finalResponseCommunity[index].like_count = res.data.total_like;
            this.finalResponseCommunity[index].is_like = 0;
          }
          this.spinner.hide();
        } else {
          this.spinner.hide();
        }
      }
    },
      (error) => {
        this.spinner.hide();
        this.generalService.getErrorMsg(error.error.message);
      });
  }
  loadMore() {
    this.current_page++;
    this.getCommunityDetails();
  }

  commentFormSubmit: boolean = false;
  generateCommentFormObj() {
    this.commentForm = this.formBuilder.group({
      project_id: [this.id],
      comment: ['', [Validators.required, Validators.maxLength(500)]],
    });
  }
  get commentControl() { return this.commentForm.controls; }
  addComment(community_id: any, project_id: any, index: number) {
    this.commentFormSubmit = true;
    if (this.commentForm.invalid) { return }
    this.spinner.show();
    const postData = {
      'project_id': project_id,
      'project_community_id': community_id,
      'comment': this.commentForm.value.comment
    };

    this.appSettingsService.addComment(postData).subscribe((res: any) => {
      if (res) {
        this.spinner.hide();

        if (res.success) {
          this.toastr.success(res.message, "success");
          this.commentFormSubmit = false;
          this.finalResponseCommunity[index].comments.unshift(res.data);
          this.commentForm.controls['comment'].setValue('');
          this.finalResponseCommunity[index].comment_count = res.data.total_comment;
        } else {
          this.generalService.getErrorMsg(res.message);
          this.spinner.hide();
          this.commentFormSubmit = false;
        }
      }
    },
      (error) => {
        this.generalService.getErrorMsg(error.error.message);
      });
  }
  // community: ends

  getProjectDetails() {
    this.spinner.show();
    this.appSettingsService.getProjectDetails(this.id).subscribe((res: any) => {
      if (res.success) {
        this.spinner.hide();
        this.projectDetails = res.data.response;
        for (let prrojectDonationType of this.projectDetails['project_donation_type']) {
          if (prrojectDonationType == 1) {
            this.isDonationTypeAvailable = true;
          }
          if (prrojectDonationType == 2) {
            this.isVolunteerTypeAvailable = true;
          }
        }
        this.getProjectList(this.projectDetails.category_id);
        this.getCommunityDetails();
        this.project_documents = this.projectDetails.project_documents

      }
    },
      (error) => {
        this.generalService.getErrorMsg(error.error.message);
      });
    this.generateDonationPurposeFormObj();
  }
  generateDonationPurposeFormObj() {
    this.donationPurposeForm = this.formBuilder.group({
      id: [this.id],
      donation_purpose: ['', Validators.required],
    });
  }
  get donationPurpose() { return this.donationPurposeForm.controls; }
  generateVolunteerFormObj() {
    this.volunteerForm = this.formBuilder.group({
      project_id: [this.id],
      email: ['', [Validators.required, Validators.email, Validators.maxLength(100)]],
      volunteer_type: ['', Validators.required],
      comment: ['', [Validators.required, Validators.maxLength(100)]],
    });
  }
  get volunteerControl() { return this.volunteerForm.controls; }
  onTimeDonationFormObj() {
    this.oneTimeDonationForm = this.formBuilder.group({
      project_id: [this.id],
      email: ['', [Validators.required, Validators.email, Validators.maxLength(100)]],
      comment: ['', [Validators.maxLength(100)]],
      donation_amount: [0, [Validators.required, Validators.pattern(/^\d+(\.\d{1,2})?$/), this.priceLimitValidator(1000000000)]],
      tips_amount: [0, [Validators.pattern(/^\d+(\.\d{1,2})?$/)]],
      card_number: ['', [Validators.required, Validators.pattern("^[0-9]{12,19}")]],
      expire_date: ['', Validators.required],
      cvv: ['', [Validators.required, Validators.pattern("^[0-9]{3,4}")]],
      is_card_save: [''],
      type: [1],
    });
  }
  recuringDonationFormObj() {
    this.recuringDonationForm = this.formBuilder.group({
      project_id: [this.id],
      email: ['', [Validators.required, Validators.email, Validators.maxLength(100)]],
      comment: ['', [Validators.maxLength(100)]],
      donation_amount: [0, [Validators.required, Validators.pattern(/^\d+(\.\d{1,2})?$/), this.priceLimitValidator(1000000000)]],
      tips_amount: [0, [Validators.pattern(/^\d+(\.\d{1,2})?$/)]],
      card_number: ['', [Validators.required, Validators.pattern("^[0-9]{12,19}")]],
      expire_date: ['', Validators.required],
      cvv: ['', [Validators.required, Validators.pattern("^[0-9]{3,4}")]],
      is_monthly: [''],
      recurring_date: ['', Validators.required],
      is_card_save: [''],
      type: [2],
    });
  }
  get oneTimeDonationControl() { return this.oneTimeDonationForm.controls; }
  get recuringDonationControl() { return this.recuringDonationForm.controls; }
  onTabClick(event: any) {
    let tabIndex = event.index;
    if (tabIndex && tabIndex === 1) {
      let isAuth = this.authService.isLoggedIn();
      if (!isAuth) {
        this.router.navigate(['/login']);
      }
      // this.feelGoodPortalList();
      this.getCommunityDetails();
    }
  }

  // feelGoodPortalList() {
  //   const postData = { 'page': this.current_page };
  //   this.appSettingsService.getFeelGoodPortalList(postData).subscribe((res) => {
  //     if (res.success === true) {
  //       this.responseFlag = true;
  //       if (res.data && res.data.response && res.data.response.length > 0) {
  //         this.index = (this.current_page - 1) * res.data.pagination.per_page + 1;
  //         this.limit = res.data.pagination.per_page;
  //         this.finalResponse = [];
  //         let currentPaginationObject = {
  //           range: this.current_page,
  //           total: res.data.pagination.total,
  //         };
  //         this.pager = GeneralFunction.setPagination(
  //           currentPaginationObject,
  //           this.current_page,
  //           this.limit
  //         );
  //         this.finalResponse = res.data.response;
  //       } else {
  //         this.finalResponse = [];
  //         this.noDataFlag = "Data Not Found";
  //       }
  //     } else {
  //       this.finalResponse = [];
  //       this.noDataFlag = "Data Not Found";
  //     }
  //     this.spinner.hide();
  //   },
  //     (error) => {
  //       this.generalService.getErrorMsg(error.error.message);
  //     });
  // }

  setCurrent(pageNumber: number) {
    if (pageNumber > 0 && $.inArray(pageNumber, this.pager.pages) !== -1) {
      this.current_page = pageNumber;
      this.offset = (pageNumber - 1) * this.limit;
      this.start_record = this.offset + 1;
      // this.feelGoodPortalList();
      this.getCommunityDetails();
    }
  }
  getProjectList(category: any) {
    const postData = { 'category': category };
    this.appSettingsService.getAllProjectsList(postData).subscribe((res) => {
      if (res.success === true) {
        this.responseFlag = true;
        if (res.data && res.data.response && res.data.response.length > 0) {
          this.finalResponse = res.data.response;
        } else {
          this.finalResponse = [];
          this.noDataFlag = "Data Not Found";
        }
      } else {
        this.noDataFlag = "Data Not Found";
      }
      this.spinner.hide();
    });
  }
  dontionTypeNext() {
    this.donationTypeFormSubmit = true;
    if (this.donationPurposeForm.invalid) { return }
    this.isDonationTypeForm = false;
    if (this.donationPurposeForm.value.donation_purpose == 2) {
      this.isVolunteerForm = true;
    }
    if (this.donationPurposeForm.value.donation_purpose == 1) {
      this.isDonationForm = true;
    }
  }
  addVolunteer() {
    this.volunteerFormSubmit = true;
    if (this.volunteerForm.invalid) { return }
    this.spinner.show();
    this.appSettingsService.addVolunteer(this.volunteerForm.value).subscribe((res: any) => {
      if (res) {
        this.spinner.hide();
        if (res.success) {
          this.toastr.success(res.message, "success");
          this.isVolunteerFormSuccess = true;
          this.isVolunteerForm = false;
          this.getCommunityDetails('volunteer');
        } else {
          this.generalService.getErrorMsg(res.message);
          this.spinner.hide();
        }
      }
    },
      (error) => {
        this.generalService.getErrorMsg(error.error.message);
      });
  }
  back(type: any) {

    if (type == 'volunteer') {
      this.isDonationTypeForm = true;
      this.isVolunteerForm = false;
    }
    if (type == 'donation') {
      this.isDonationTypeForm = true;
      this.isDonationForm = false;
    }
    if (type == 'success') {
      this.isDonationTypeForm = true;
      this.isVolunteerFormSuccess = false;
      this.isDonationFormSuccess = false;
      this.resmoveFormValue();
      this.generateVolunteerFormObj();
    }
  }
  inputnumber = 0;
  inputAmount: number = 0;
  inputTips: number = 0;
  plus(type: any, formType: any = '') {
    if (type == 'amount') {
      if (formType == 'recuring') {
        this.inputAmount = this.recuringDonationForm.value.donation_amount;
        this.inputAmount = Number(this.inputAmount) + 1;
        this.recuringDonationForm.controls['donation_amount'].setValue(this.inputAmount);
      } else {
        this.inputAmount = this.oneTimeDonationForm.value.donation_amount;
        this.inputAmount = Number(this.inputAmount) + 1;
        this.oneTimeDonationForm.controls['donation_amount'].setValue(this.inputAmount);
      }

    }
    if (type == 'tips') {
      if (formType == 'recuring') {
        this.inputTips = this.recuringDonationForm.value.tips_amount;
        this.inputTips = Number(this.inputTips) + 1;
        this.recuringDonationForm.controls['tips_amount'].setValue(this.inputTips);
      } else {
        this.inputTips = this.oneTimeDonationForm.value.tips_amount;
        this.inputTips = Number(this.inputTips) + 1;
        this.oneTimeDonationForm.controls['tips_amount'].setValue(this.inputTips);
      }

    }
    this.totalAmountDisplay(formType);
  }
  minus(type: any, formType: any = '') {
    if (type == 'amount') {
      if (formType == 'recuring') {
        this.inputAmount = this.recuringDonationForm.value.donation_amount;
        if (this.inputAmount > 0) {
          this.inputAmount = Number(this.inputAmount) - 1;
          this.recuringDonationForm.controls['donation_amount'].setValue(this.inputAmount);
        } else {
          this.recuringDonationForm.controls['donation_amount'].setValue(0);
        }
      } else {
        if (formType == 'recuring') {
          this.inputAmount = this.recuringDonationForm.value.donation_amount;
          if (this.inputAmount > 0) {
            this.inputAmount = Number(this.inputAmount) - 1;
            this.recuringDonationForm.controls['donation_amount'].setValue(this.inputAmount);
          } else {
            this.recuringDonationForm.controls['donation_amount'].setValue(0);
          }
        }
      }
    }
    if (type == 'tips') {
      if (formType == 'recuring') {
        this.inputTips = this.recuringDonationForm.value.tips_amount;
        if (this.inputTips > 0) {
          this.inputTips = Number(this.inputTips) - 1;
          this.recuringDonationForm.controls['tips_amount'].setValue(this.inputTips);
        } else {
          this.recuringDonationForm.controls['tips_amount'].setValue(0);
        }
      } else {
        this.inputTips = this.oneTimeDonationForm.value.tips_amount;
        if (this.inputTips > 0) {
          this.inputTips = Number(this.inputTips) - 1;
          this.oneTimeDonationForm.controls['tips_amount'].setValue(this.inputTips);
        } else {
          this.oneTimeDonationForm.controls['tips_amount'].setValue(0);
        }
      }
    }
    this.totalAmountDisplay(formType);
  }
  oneTimeDonationFormSubmit: boolean = false;
  addOneTimeDonation() {
    this.oneTimeDonationFormSubmit = true;
    if (this.oneTimeDonationForm.value.donation_amount < 1) {
      this.oneTimeDonationForm.controls['donation_amount'].setValue(0);
    }
    if (this.oneTimeDonationForm.value.tips_amount < 1) {
      this.oneTimeDonationForm.controls['tips_amount'].setValue(0);
    }
    if (this.oneTimeDonationForm.invalid) { return }

    this.mergeArray(this.oneTimeDonationForm.value, '');
    this.addDonation(this.formData);
  }
  recuringDonationFormSubmit: boolean = false;
  addRecuringDonation() {
    this.recuringDonationFormSubmit = true;
    if (this.recuringDonationForm.value.donation_amount < 1) {
      this.recuringDonationForm.controls['donation_amount'].setValue(0);
    }
    if (this.recuringDonationForm.value.tips_amount < 1) {
      this.recuringDonationForm.controls['tips_amount'].setValue(0);
    }
    if (this.recuringDonationForm.invalid) { return }

    this.mergeArray(this.recuringDonationForm.value, 'recuring');
    this.addDonation(this.recuringFormData);
  }
  addDonation(formData: any) {
    this.spinner.show();
    this.appSettingsService.addDonation(formData).subscribe((res: any) => {
      if (res) {
        this.spinner.hide();

        if (res.success) {
          this.toastr.success(res.message, "success");
          this.isDonationFormSuccess = true;
          this.isVolunteerForm = false;
          this.isDonationForm = false;
          this.getCommunityDetails('donation');
        } else {
          this.generalService.getErrorMsg(res.message);
          this.spinner.hide();
        }
      }
    },
      (error) => {
        this.generalService.getErrorMsg(error.error.message);
      });
  }
  totalAmountDisplay(type: string = '', inputType: string = '') {
    if (type == 'recuring') {
      this.totalAmount = Number(this.recuringDonationForm.value.donation_amount) + Number(this.recuringDonationForm.value.tips_amount);
    } else {
      this.totalAmount = Number(this.oneTimeDonationForm.value.donation_amount) + Number(this.oneTimeDonationForm.value.tips_amount);
    }


  }
  recuringType(event: any) {
    const isChecked = event.target.checked;

    // if (isChecked) {
    //   this.isDispalayDatepicker = true;
    //   this.recuringDonationForm.controls['recurring_date'].setValidators([Validators.required]);
    // } else {
    //   this.isDispalayDatepicker = false;
    //   this.recuringDonationForm.controls['recurring_date'].setValidators([]);
    // }
    // this.recuringDonationForm.controls['recurring_date'].updateValueAndValidity();
  }
  uploadDocument(event: any, type: string = '') {
    let file = event.target.files[0];
    const fileArray = {
      'image_video': ['image/jpeg', 'image/png', 'video/mp4', 'video/flv', 'video/wmv']
    };
    this.fileImgTypeSupport = fileArray.image_video.join(',');
    this.fileArrayType = fileArray.image_video;
    if ($.inArray(file.type, this.fileArrayType) !== -1) {
      if (type == 'recuring') {
        this.recuringFormData.append('document', event.target.files[0]);
        this.recuringFileName = file.name;
      } else {
        this.formData.append('document', event.target.files[0]);
        this.donationFileName = file.name;
      }
    } else {
      if (type == 'recuring') {
        this.recuringImageFileValid = true;
      } else {
        this.oneTimeImageFileValid = true;
      }
    }
  }
  mergeArray(formArray: any, type: string = '') {
    if (type == 'recuring') {
      Object.keys(formArray).forEach(key => {
        if (this.recuringFormData.has(key)) {
          this.recuringFormData.set(key, formArray[key]);
        } else {
          this.recuringFormData.append(key, formArray[key]);
        }
      })
    }
    else if (type == 'community') {
      Object.keys(formArray).forEach(key => {
        if (this.communityFormData.has(key)) {
          this.communityFormData.set(key, formArray[key]);
        } else {
          this.communityFormData.append(key, formArray[key]);
        }
      })
    } else {
      Object.keys(formArray).forEach(key => {
        if (this.formData.has(key)) {
          this.formData.set(key, formArray[key]);
        } else {
          this.formData.append(key, formArray[key]);
        }
      })
    }
  }
  resmoveFormValue() {
    this.onTimeDonationFormObj();
    this.recuringDonationFormObj();
    this.donationFileName = '';
    this.recuringFileName = '';
    this.isDispalayDatepicker = false;
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
  priceTipsLimitValidator(limit: number): ValidatorFn {
    return (control: AbstractControl): ValidationErrors | null => {
      const price = parseFloat(control.value);
      if (isNaN(price) || price < 0 || price > limit) {
        return { priceLimit: true };
      }
      return null;
    };
  }
  showComment: any = [];
  // hideShowCommentBox(i: any) {
  //   this.showComment[i] = !this.showComment[i];
  // }
  hideShowCommentBox(i:any){
    if(this.token || this.finalResponseCommunity[i].comments.length > 0){
      this.showComment[i] = !this.showComment[i];
    }
    
  }
}

