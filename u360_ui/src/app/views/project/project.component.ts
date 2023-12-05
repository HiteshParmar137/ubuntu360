
import { Component, OnInit, Renderer2, ViewChild } from '@angular/core';
import { Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import { GeneralFunction } from 'src/app/_directives/general-function.directive';
import { Options } from '@angular-slider/ngx-slider';
import { GeneralService } from 'src/app/services/general.service';
import { NgbModal, ModalDismissReasons } from '@ng-bootstrap/ng-bootstrap';
import {
  AbstractControl,
  UntypedFormBuilder,
  UntypedFormGroup,
  Validators,
} from '@angular/forms';
@Component({
  selector: 'app-project',
  templateUrl: './project.component.html',
  styleUrls: ['./project.component.scss'],
})

export class ProjectComponent implements OnInit {
  @ViewChild('closebutton') closebutton: any;
  currentRate = 0;
  userId: string = "";
  userProfile: string = "";
  category: string = "";
  categories: any = [];
  finalResponse: any = [];
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
  followObj: any = {};
  options: Options = {
    floor: 0,
    ceil: 100
  };
  projectId: any = '';
  search: string = "";
  closeResult: string = '';
  submitted = false;
  isLodeMore = false;
  reviewForm: UntypedFormGroup = new UntypedFormGroup({});
  constructor(
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private appSettingsService: AppSettingsService,
    public router: Router,
    public generalService: GeneralService,
    private renderer: Renderer2,
    private modalService: NgbModal,
    private formBuilder: UntypedFormBuilder,
  ) {
  }

  ngOnInit(): void {
    this.getProjectList();
    this.getCategoriesList();
    this.userId = localStorage.getItem('userId') ?? '';
    this.userProfile = localStorage.getItem('image') ?? '';
    this.generateFormObj();
  }
  /**
   * This function is created for generate review form builder
   *
   * @return response()
   */
  generateFormObj() {
    this.reviewForm = this.formBuilder.group({
      projectId: [''],
      comment: ['', [Validators.required]],
      rating: ['', [Validators.required]],
    });
  }
  get reviewFormControls(): { [key: string]: AbstractControl } {
    return this.reviewForm.controls;
  }
  /**
   * This function is created for get project list
   *
   * @return response()
   */
  getProjectList() {
    const postData = { 'page': this.current_page, 'title': this.search, 'category': this.category };
    this.appSettingsService.getAllProjectsList(postData).subscribe({
      next: (res) => {
        if (res.success === true) {
          this.responseFlag = true;
          if (res.data.response.length > 9) {
            this.isLodeMore = true;
          } else {
            this.isLodeMore = false;
          }
          if (res.data?.response?.length > 0) {
            this.index = (this.current_page - 1) * res.data.pagination.per_page + 1;
            this.limit = res.data.pagination.per_page;
            let currentPaginationObject = {
              range: this.current_page,
              total: res.data.pagination.total,
            };
            this.pager = GeneralFunction.setPagination(
              currentPaginationObject,
              this.current_page,
              this.limit
            );
            this.finalResponse = this.finalResponse.concat(res.data.response);
          } else {
            this.finalResponse = [];
            this.noDataFlag = "Data Not Found";
          }
        } else {
          this.noDataFlag = "Data Not Found";
        }
      }
    });
  }
  /**
   * This function is created for get category list
   *
   * @return response()
   */
  getCategoriesList() {
    this.appSettingsService.getCategoriesList().subscribe({
      next: (res: any) => {
        if (res.success === true) {
          this.responseFlag = true;
          if (res.data && res.data.length > 0) {
            this.categories = res.data;
          }
        }
      },
      error: (error) => {
        this.generalService.getErrorMsg(error.error.message);
      }
    });
  }
  setCurrent(pageNumber: number) {
    if (pageNumber > 0 && $.inArray(pageNumber, this.pager.pages) !== -1) {
      this.current_page = pageNumber;
      this.offset = (pageNumber - 1) * this.limit;
      this.start_record = this.offset + 1;
      this.getProjectList();
    }
  }

  /**
   * This function is created for search project by category
   *
   * @return response()
   */
  searchProject(category: any) {
    if (category != '') {
      this.category = category;
    } else {
      this.category = '';
    }
    this.current_page = 1;
    this.finalResponse = [];
    this.getProjectList();
  }

  /**
  * This function is created for  follow and unfollow project
  *
  * @return response()
  */
  async followUnfollow(event: any, id: any) {
    let type = '';
    if (event.target.innerHTML == 'Unfollow') {
      type = 'unfollow';
    } else {
      type = 'follow';
    }
    this.followObj = {
      'project_id': id,
      'type': type,
    };
    let text = '';
    if (type == 'follow') {
      text = 'follow';
    } else {
      text = 'unfollow';
    }
    let deleteArray = { 'title': 'Are you sure?', 'text': 'You want to ' + text + ' this project?', 'data': id, 'api_name': 'follow/create' };
    await this.generalService.openDeleteConfirmationDialog(deleteArray).then(response => {
      if (response) {
        this.appSettingsService.followUnfollowProject(this.followObj).subscribe({
          next: (res: any) => {
            if (res) {
              if (res.success) {
                this.toastr.success(res.message, "Success");
                if (event.target.innerHTML == 'Unfollow') {
                  this.renderer.setProperty(event.target, 'textContent', 'Follow');
                  type = 'follow';
                } else {
                  this.renderer.setProperty(event.target, 'textContent', 'Unfollow');
                  type = 'unfollow';
                }
              } else {
                this.toastr.error(res.message, "error");
              }
            }
          },
          error: (error) => {
            this.generalService.getErrorMsg(error.error.message);
          }
        });
      }
    })
  }

  /**
  * This function is created for load more project
  *
  * @return response()
  */
  loadMore() {
    this.current_page++;
    this.getProjectList();
  }

  /**
   * Write code on Method
   *
   * @return response()
   */
  open(content: any, id: any) {
    this.submitted = false;
    this.reviewForm.controls['projectId'].setValue(id);
    this.reviewForm.controls['rating'].setValue('');
    this.reviewForm.controls['comment'].setValue('');
    this.modalService.open(content, { ariaLabelledBy: 'modal-basic-title' }).result.then((result) => {
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
      return `with: ${reason}`;
    }
  }

  /**
   * this function is created for review form submit
   *
   * @return response()
   */
  reviewFormSubmit(): void {
    this.submitted = true;
    if (this.reviewForm.invalid) {
      return;
    }
    this.appSettingsService.saveProjectReview(this.reviewForm.value).subscribe({
      next: (res: any) => {
        if (res) {
          if (res.success) {
            this.toastr.success(res.message, "Success");
            this.modalService.dismissAll();
          } else {
            this.generalService.getErrorMsg(res.message);
          }
        }
      },
      error: (error) => {
        this.generalService.getErrorMsg(error.error.message);
      }
    });
  }

  /**
   * this function is created for reset form
   *
   * @return response()
   */
  reset() {
    this.category = '';
    this.search = '';
    this.current_page = 1;
    this.finalResponse = [];
    this.getProjectList();
  }

  /**
   * create function for get donation percentage value  
   *
   * @return response()
   */
  getConcatPercentageValue(value: any) {
    return value + '%';
  }
}

