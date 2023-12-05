import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { NgxSpinnerService } from 'ngx-spinner';
import { ToastrService } from 'ngx-toastr';
import { AppSettingsService } from 'src/app/app-settings.service';
import { GeneralService } from 'src/app/services/general.service';
import { GeneralFunction } from "src/app/_directives/general-function.directive";

@Component({
  selector: 'app-donation',
  templateUrl: './donation.component.html',
  styleUrls: ['./donation.component.scss']
})
export class DonationComponent implements OnInit {
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
  isMyDonation:boolean=true;
  isMyProject:boolean=false;
  list:string = 'my_donation'
  last_page:string = '';
  constructor(
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    private appSettingsService: AppSettingsService,
    public router: Router,
    public generalService: GeneralService,
  ) { }

  ngOnInit(): void {
    this.getDonationDetails();
  }

  getDonationDetails()
  {
    this.spinner.show();
    const postData = { 'page': this.current_page, 'list': this.list };
    this.appSettingsService.getAllDonation(postData).subscribe({
      next:(res) => {
        if (res.success) {
          this.responseFlag = true;
          if (res.data?.response?.length > 0) {
            this.index = (this.current_page - 1) * res.data.pagination.per_page + 1;
            this.limit = res.data.pagination.per_page;
            this.last_page = res.data.pagination.last_page;
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
            this.finalResponse = res.data.response;
          } else {
            this.finalResponse = [];
            this.noDataFlag = "Data Not Found";
          }
        } else {
          this.finalResponse = [];
          this.noDataFlag = "Data Not Found";
        }
        if(this.finalResponse.length < 1){
          if(this.list=='my_donation'){
            this.noDataFlag = "My Donation Not Found.";
          }else{
            this.noDataFlag = "My Projects Donation Not Found.";
          }        
        }
        this.spinner.hide();
      },
      error:(error) => {             
        this.generalService.getErrorMsg(error.error.message);
      }
    });
  }
  donationList(type:any){
    if(type=='my-donation'){
      this.list = 'my_donation';
      this.setCurrent(1);
      this.isMyDonation=true;
      this.isMyProject=false;
    }
    if(type=='my-project'){
      this.list = 'my_project_donation';
      this.setCurrent(1);
      this.isMyDonation=false;
      this.isMyProject=true;
    }
  }

  setCurrent(pageNumber: number) {
    if (pageNumber > 0 && $.inArray(pageNumber, this.pager.pages) !== -1) {
      this.current_page = pageNumber;
      this.offset = (pageNumber - 1) * this.limit;
      this.start_record = this.offset + 1;
      this.getDonationDetails();
    }
  }
  async stopRecurring(donationId:any){
    let deleteArray = { 'title': 'Are you sure?', 'text': 'You want to stop recurring donation?', 'data': donationId, 'api_name': 'close' };
    const confirmarion = await this.generalService.openDeleteConfirmationDialog(deleteArray);
    if (confirmarion) {
      this.spinner.show();
      const postData = { 'donation_id':donationId};
      this.appSettingsService.stopDonationRecurring(postData).subscribe({
        next:(res: any) => {
          if (res) {
            if (res.success === true) {
              this.getDonationDetails();
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
}
