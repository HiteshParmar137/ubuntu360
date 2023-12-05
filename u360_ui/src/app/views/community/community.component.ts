import { Component, OnInit, Renderer2 } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import { AuthService } from '../front-user/auth/auth.service';
import { UntypedFormBuilder, UntypedFormGroup, Validators } from '@angular/forms';
import { GeneralService } from 'src/app/services/general.service';
import { GeneralFunction } from 'src/app/_directives/general-function.directive';

@Component({
  selector: 'app-community',
  templateUrl: './community.component.html',
  styleUrls: ['./community.component.scss']
})
export class CommunityComponent implements OnInit {
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
  // donationPurposeForm: UntypedFormGroup = new UntypedFormGroup({});
  // volunteerForm: UntypedFormGroup = new UntypedFormGroup({});
  // oneTimeDonationForm: UntypedFormGroup = new UntypedFormGroup({});
  // recuringDonationForm: UntypedFormGroup = new UntypedFormGroup({});
  // communityForm: UntypedFormGroup = new UntypedFormGroup({});
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
  isDonationFormSuccess:boolean=false;
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
  recuringFileName:string='';
  donationFileName:string='';
  communityFileName:string='';
  // community variable
  communityImageFileValid: boolean = false;
  userId:any='';
  profile_image: any = '';
  sponser_type:any = '';
  token:any = '';
  name:any = '';
  user_details:any = '';
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

  ngOnInit(): void {
    this.userId = localStorage.getItem('userId') || '';
    this.getCommunityDetails();
    this.generateCommentFormObj();
    this.token = localStorage.getItem("token");
    this.profile_image = localStorage.getItem('image');
    this.sponser_type = localStorage.getItem('user_type');
    this.name = localStorage.getItem('name');
  }

  getCommunityDetails(){
    const postData = { 'page': this.current_page};
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
          this.finalResponseCommunity = this.finalResponseCommunity.concat(res.data.response);
          this.topDoners = res.data.top_donation;
          this.user_details = res.data.user_details; 
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
    },
      (error) => {
        this.generalService.getErrorMsg(error.error.message);
      });
  }

  likeUnlike(community_id:any, project_id:any, type:any, event:any,index:number) {
    const postData = { 'project_community_id': community_id, 'project_id': project_id, 'type':type};
    this.appSettingsService.likeUnlike(postData).subscribe((res: any) => {
      if (res) {
        if (res.success) {
              if (type=='like') {
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
        }
      }
    },
    (error) => {
      this.generalService.getErrorMsg(error.error.message);
    });
  }
  loadMore(){
    this.current_page++;
    this.getCommunityDetails();
  }

  commentFormSubmit:boolean = false;
  generateCommentFormObj() {
    this.commentForm = this.formBuilder.group({
      project_id: [this.id],
      comment: ['', [Validators.required, Validators.maxLength(500)]],
    });
  }

  get commentControl() { return this.commentForm.controls; }
  addComment(community_id:any, project_id:any, index:number){
    this.commentFormSubmit = true;
    if (this.commentForm.invalid) { return }
    const postData = {
      'project_id':project_id,
      'project_community_id':community_id,
      'comment':this.commentForm.value.comment
    };
     
    this.appSettingsService.addComment(postData).subscribe({
      next:(res: any) => {
        if (res) {
          if (res.success) {
            this.toastr.success(res.message, "success");
            this.commentFormSubmit = false;
            this.finalResponseCommunity[index].comments.unshift(res.data);
            this.commentForm.controls['comment'].setValue('');
            this.finalResponseCommunity[index].comment_count = res.data.total_comment;
          } else {
            this.generalService.getErrorMsg(res.message);
            this.commentFormSubmit = false;
          }
        }
      },
      error: (error) => {    
        this.generalService.getErrorMsg(error.error.message);
      }
    });
  }

  showComment: any= [];
  hideShowCommentBox(i:any){
    if(this.token || this.finalResponseCommunity[i].comments.length > 0){
      this.showComment[i] = !this.showComment[i];
    }
    
  }
}
