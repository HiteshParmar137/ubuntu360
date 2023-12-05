import { Component, OnInit } from '@angular/core';
import { AbstractControl, FormArray, FormControl, UntypedFormBuilder, UntypedFormGroup, Validators, FormGroup, FormBuilder } from '@angular/forms';
import { ActivatedRoute, NavigationEnd, Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import { GeneralService } from 'src/app/services/general.service';
import { OwlOptions } from 'ngx-owl-carousel-o';
import { GeneralFunction } from "src/app/_directives/general-function.directive";
@Component({
  selector: 'app-view',
  templateUrl: './view.component.html',
  styleUrls: ['./view.component.scss']
})
export class ViewComponent implements OnInit {
  id: any = '';
  projectDetails: any = [];
  images: any[] = [];
  videos: any[] = [];
  documents: any[] = [];
  imageAlbum: any[] = [];
  videoAlbum: any[] = [];
  documentAlbum: any[] = [];
  finalResponse: [] = [];
  responseFlag: boolean = false;
  noDataFlag: string = "";
  noReviewDataFlag: string = "";
  href: string = "";
  referrer: string = "";
  fromMyProject: boolean = false;
  fromMyFollowed: boolean = false;
  fromCompleted: boolean = false;
  allReviews: any = [];
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
  isCompleted: boolean = false;
  followObj: any = {};
  projectListType:any='list';
  constructor(
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private formBuilder: UntypedFormBuilder,
    private route: ActivatedRoute,
    public router: Router,
    public generalService: GeneralService,
    private appSettingsService: AppSettingsService,
    private fb: FormBuilder,
  ) { }
  customOptions: OwlOptions = {
    loop: false,
    mouseDrag: true,
    touchDrag: true,
    pullDrag: false,
    dots: false,
    nav:true,
    navSpeed: 600,
    margin:30,
    autoHeight:true,
    responsive: {
      0: {
        items: 1 
      },
      400: {
        items: 2
      },
      760: {
        items: 2,
        margin:20
      },
      1000: {
        items: 3
      }
    },
  }

  ngOnInit(): void {
    this.id = this.route.snapshot.params['id'];
    this.referrer = this.route.snapshot.params['referrer'];
    this.getProjectDetails();

    if (this.referrer == 'my_projects') {
      this.fromMyProject = true;
    } else if (this.referrer == 'followed') {
      this.fromMyFollowed = true;
    } else if (this.referrer == 'completed') {
      this.fromCompleted = true;
    }
    this.projectListType= localStorage.getItem("project_list_type") ?? 'list';
  }

  closeProject() {
    this.spinner.show();
    this.appSettingsService.closeProject(this.id).subscribe((res: any) => {
      if (res) {
        if (res.success == true) {
          this.toastr.success(res.message, "success");
          this.getProjectDetails();
        } else {
          this.generalService.getErrorMsg(res.message);
        }
        this.spinner.hide();
      }
    });
  }
  getProjectDetails() {
    this.spinner.show();
    this.appSettingsService.getProjectDetails(this.id).subscribe({
      next:(res: any) => {
        if (res.success) {
          this.spinner.hide();
          this.projectDetails = res.data.response;
          this.getProjectList(this.projectDetails.category_id);
          this.getAllReviews(this.projectDetails.project_id);
          // this.project_documents = this.projectDetails.project_documents
          if (this.projectDetails.status == "Completed by Owner") {
            this.isCompleted = true;
          } else {
            this.isCompleted = false;
          }

        }
      },
      error:(error) => {             
        this.generalService.getErrorMsg(error.error.message);
      }
    });
  }
  getProjectList(category:any) {
    const postData = {'category':category};
    this.appSettingsService.getAllProjectsList(postData).subscribe((res) => {
      if (res.success === true) {
        this.responseFlag = true;
        if (res.data?.response?.length > 0) {
          this.finalResponse=res.data.response;
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

  getAllReviews(project_id:any) {
    const postData = { 'page': this.current_page, 'project_id': project_id};
    this.appSettingsService.getAllProjectReviews(postData).subscribe((res) => {
      if (res.success === true) {
        this.responseFlag = true;
        if (res.data?.response?.sall_reviews.length > 0) {
          this.index = (this.current_page - 1) * res.data.pagination.per_page + 1;
          this.limit = res.data.pagination.per_page;
          this.finalResponse = [];
          let currentPaginationObject = {
            range: this.current_page,
            total: res.data.pagination.total,
          };
          this.pager = GeneralFunction.setPagination(
            currentPaginationObject,
            this.current_page,
            this.limit
          );
          this.allReviews=res.data.response.all_reviews;          
        } else {
          this.allReviews = [];
          this.noReviewDataFlag = "Data Not Found";
        }
      } else {
        this.noReviewDataFlag = "Data Not Found";
      }
      this.spinner.hide();
    });
  }

  setCurrent(pageNumber: number) {
    if (pageNumber > 0 && $.inArray(pageNumber, this.pager.pages) !== -1) {
      this.current_page = pageNumber;
      this.offset = (pageNumber - 1) * this.limit;
      this.start_record = this.offset + 1;
      this.getProjectDetails();
    }
  }

  async completeProject(id: any) {
    let deleteArray = { 'title': 'Are you sure?', 'text': 'You want to complete this project?', 'data': id, 'api_name': 'close' };
    const confirmarion = await this.generalService.openDeleteConfirmationDialog(deleteArray);
    if (confirmarion) {
      this.spinner.show();
        this.appSettingsService.closeProject(id).subscribe({
          next:(res: any) => {
            if (res) {
              if (res.success) {
                this.getProjectDetails();
                this.spinner.hide();
                this.toastr.success(res.message, "Success");
              } else {
                this.spinner.hide();
                this.toastr.error(res.message, "error");
              }
            }
          },
          error:(error) => {             
            this.generalService.getErrorMsg(error.error.message);
          }
        });
      }
  }

  followUnfollow(id: any, type: any) {
    this.followObj = {
      'project_id': id,
      'type': type,
    };
    let deleteArray = { 'title': 'Are you sure?', 'text': 'You want to '+type+' this project?', 'data': id, 'api_name': 'follow/create' };
    this.generalService.openDeleteConfirmationDialog(deleteArray).then(response => {
      if (response) {
        this.spinner.show();
        this.appSettingsService.followUnfollowProject(this.followObj).subscribe({
          next:(res: any) => {
            if (res) {
              if (res.success) {
                this.toastr.success(res.message, "Success");
                this.getProjectDetails();
              } else {
                this.toastr.error(res.message, "error");
              }
            }
          },
          error:(error) => {             
            this.generalService.getErrorMsg(error.error.message);
          }
        });
      }
      this.spinner.hide();
    });
  }
}
