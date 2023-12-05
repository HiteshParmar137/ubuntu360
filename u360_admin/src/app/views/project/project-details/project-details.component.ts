import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import { AuthService } from '../../auth/auth.service';
import { GeneralService } from 'src/app/services/general.service';
import { Location } from '@angular/common';
import {NgbModal, ModalDismissReasons} from '@ng-bootstrap/ng-bootstrap';
import {
  AbstractControl,
  UntypedFormBuilder,
  UntypedFormGroup,
  Validators,
} from '@angular/forms';
@Component({
  selector: 'app-project-details',
  templateUrl: './project-details.component.html',
  styleUrls: ['./project-details.component.scss']
})
export class ProjectDetailsComponent implements OnInit {

  id: string = '';
  projectDetails: any = [];
  finalResponse: [] = [];
  responseFlag: boolean = false;
  noDataFlag: string = "";
  page: number = 1;
  total: number = 0;
  per_page: number = 0;
  current_page: any = 1;
  limit: any;
  offset: any;
  perPageRecord = [];
  pager: any = {};
  index: number = 0;
  start_record: any;
  isLoggedIn: boolean = false;
  projectList: any =[];
  coverImage: string='';
  images: any[] = [];
  imageAlbum: any[] = [];
  videos: any[] = [];
  videoAlbum: any[] = [];
  documents: any[] = [];
  documentAlbum: any[] = [];
  rejectForm: UntypedFormGroup = new UntypedFormGroup({});
  submitted:boolean=false;
  closeResult: string = '';
  countryName:string = '';
  isImagesEmpty:boolean=false;
  isVideosEmpty:boolean=false;
  isdocumentsEmpty:boolean=false;
  constructor(
    private activeRoute: ActivatedRoute,
    private appSettingsService: AppSettingsService,
    private spinner: NgxSpinnerService,
    private toastr: ToastrService,
    private authService: AuthService,
    private router: Router,
    public generalService: GeneralService,
    private location: Location,
    private formBuilder: UntypedFormBuilder,
    private modalService: NgbModal,
  ) { }

  ngOnInit(): void {
    this.id = this.activeRoute.snapshot.params['id'];
    this.getProjectDetails();   
    if(this.authService.isLoggedIn()){
      this.isLoggedIn = true;
    } 
    this.generateRejectFormObj();
  }
  generateRejectFormObj() {
    this.rejectForm = this.formBuilder.group({
      project_id:[this.id],
      comment: ['', [Validators.required, Validators.maxLength(250)]],
    });
  }
  get rejectFormControls() { return this.rejectForm.controls; }
  getProjectDetails() {
    this.spinner.show();
    this.appSettingsService.getProjectDetails(this.id).subscribe((res: any) => {
      if (res.length > 0 && res[0].success === true) {
        this.spinner.hide();
        this.projectDetails = res[0].data;
        
        this.getCountryName(this.projectDetails.country);
        
        this.getProjectList(this.projectDetails.category_id);
        if (res[0].success === true) {
          this.projectDetails = res[0].data;
          if(this.projectDetails.default_image_name){
            this.coverImage=this.projectDetails.default_image;
          }
          
        }

        if (res[1].success === true) {
          this.images = res[1].data;
        }else{
          this.isImagesEmpty=true;
        }
        if (res[2].success == true) {
          this.videos = res[2].data;
        }else{
          this.isVideosEmpty=true;
        }
        if (res[3].success == true) {
          this.documents = res[3].data;
        }else{
          this.isdocumentsEmpty=true;
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
      }
    },
    (error) => {     
      this.spinner.hide();        
      this.generalService.getErrorMsg(error.error.message);
    });
  }

  getProjectList(category:any) {
    const postData = {'category':category};
    this.appSettingsService.getProjectsList(postData).subscribe((res) => {
      if (res.success === true) {
        this.responseFlag = true;
        if (res.data && res.data.response && res.data.response.length > 0) {
          this.finalResponse=res.data.response;
        } else {
          this.finalResponse = [];
          this.noDataFlag = "Data Not Found";
        }
      } else {       
        this.noDataFlag = "Data Not Found";
      }
      this.spinner.hide();
    },
    (error) => {     
      this.spinner.hide();        
      this.generalService.getErrorMsg(error.error.message);
    });
  }

  onTabClick(event: any) {
    let tabIndex = event.index;
    if (tabIndex && tabIndex === 1) {
      let isAuth = this.authService.isLoggedIn();
      if (!isAuth) {
        this.router.navigate(['/login']);
      }
    }
  }

  goBack(){
    this.location.back();
  }

  open(content:any) {
    this.submitted=false;
    this.rejectForm.controls['project_id'].setValue(this.id);
    this.rejectForm.controls['comment'].setValue('');
    this.modalService.open(content, {ariaLabelledBy: 'modal-basic-title'}).result.then((result) => {
      this.closeResult = `Closed with: ${result}`;
    }, (reason) => {
      this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
    });
  } 
    
  /**
   * Write code on Method
   *
   * @return response()
   */
  private getDismissReason(reason: any): string {
    if (reason === ModalDismissReasons.ESC) {
      return 'by pressing ESC';
    } else if (reason === ModalDismissReasons.BACKDROP_CLICK) {
      return 'by clicking on a backdrop';
    } else {
      return  `with: ${reason}`;
    }
  }
  approveProject(){
    debugger;
    this.spinner.show();
    this.appSettingsService.approveProject({project_id:this.id}).subscribe((res) => {
      if (res.success === true) {
        this.toastr.success(res.message, "success");
        this.getProjectDetails();
      } else {       
        this.generalService.getErrorMsg(res.message);
      }
      this.spinner.hide();
    },
    (error) => {
      this.spinner.hide();
      this.generalService.getErrorMsg(error.error.message);
    });
  }
  rejectProject(){
    this.submitted=true;
    if (this.rejectForm.invalid) { return }
    this.spinner.show();
    this.appSettingsService.rejectProject(this.rejectForm.value).subscribe((res) => {
      if (res.success === true) {
        this.toastr.success(res.message, "success");
        this.modalService.dismissAll();
        this.getProjectDetails();
      } else {       
        this.generalService.getErrorMsg(res.message);
      }
      this.spinner.hide();
    },
    (error) => {
      this.spinner.hide();
      this.generalService.getErrorMsg(error.error.message);
    });
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
    },
    (error) => {     
      this.spinner.hide();        
      this.generalService.getErrorMsg(error.error.message);
    });
  }
}
