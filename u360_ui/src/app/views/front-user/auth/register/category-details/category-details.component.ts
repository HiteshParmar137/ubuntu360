import { Component, OnInit } from '@angular/core';
import { AppSettingsService } from 'src/app/app-settings.service';
import { Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { NgxSpinnerService } from 'ngx-spinner';
import { FormArray, FormControl, UntypedFormBuilder, UntypedFormGroup, Validators } from "@angular/forms";

@Component({
  selector: 'app-category-details',
  templateUrl: './category-details.component.html',
  styleUrls: ['./category-details.component.scss']
})
export class CategoryDetailsComponent implements OnInit {
  formData = new FormData();
  userDetails:any=[];
  categoryList:any=[];
  categoryDetails: UntypedFormGroup = new UntypedFormGroup({});
  constructor(
    private formBuilder: UntypedFormBuilder,
    private apiServvice: AppSettingsService,
    private toastr: ToastrService,
    private spinner: NgxSpinnerService,
    public router: Router,
  ) { }

  categoryOption:any=[];
  ngOnInit() {
    this.getUserDetails();
    this.generateFormObj();
  }
  getUserDetails() {
    this.apiServvice.getUserDetails().subscribe((res:any) => {
      if(res.success){
        this.userDetails = res.data.userDetails;
        this.toastr.success(res.message, "Success");
      }else{
        this.toastr.success(res.message, "error");
      }  
    });
  }
  generateFormObj(){
   
    this.categoryDetails = this.formBuilder.group({
      category_ids: this.formBuilder.array([], [Validators.required]),
    });
    let category_ids: FormArray = this.categoryDetails.get('category_ids') as FormArray;

    for (let category of this.categoryList) {
      let iscategoryExist=false;
      for (let usercategory of this.userDetails.category_ids) {
        if(category.id == usercategory.category_id){
          iscategoryExist=true;
        }
      }
      if(iscategoryExist){
        category_ids.push(new FormControl(category.id));
        category.selected=true;
        this.categoryOption.push(category);
      }else{
        category.selected=false;
        this.categoryOption.push(category)
      }
    }
  }
  get category() { return this.categoryDetails.controls; }
  saveData(){
    if (this.categoryDetails.invalid) {
      return;
    }
    this.apiServvice.saveUserDetails(this.categoryDetails.value,3,'next').subscribe((res: any) => {
      if (res) {
        if (res.success) {
          this.toastr.success(res.message, "success");
          this.router.navigate(["social-details"]);
        } else {
          let convertErrorMessage = Object.values(res.message);
          if (typeof res.message === 'object') {
            let convertArrayMessage: any[] = [];
            let i = 1;
            for (let convert of convertErrorMessage) {
              let c = i + '.' + convert + `</br>`;
              i++;
              convertArrayMessage.push(c);
            }
            if (convertArrayMessage.length === 1) {
              let singleMessage: any = Object.values(res.message)[0];
              this.toastr.error(singleMessage, 'Error');
            } else {
              let errorMessage = convertArrayMessage.join(' ');
              this.toastr.error(errorMessage, 'Error', {
                enableHtml: true,
              });
            }
          } else {
            this.toastr.error(res.message, 'Error');
          }
        }      
      }            
    });
  }
  onCheckboxChange(e: any) {
    const category_ids: FormArray = this.categoryDetails.get('category_ids') as FormArray;
    if (e.target.checked) {
      category_ids.push(new FormControl(e.target.value));
      for(let categoryOptionKey in this.categoryOption){
        if(this.categoryOption[categoryOptionKey]['id']==e.target.value){
          this.categoryOption[categoryOptionKey]['selected']=true;
        }            
      }
    } else {
      let i: number = 0;
      category_ids.controls.forEach((item: any) => {
        if (item.value == e.target.value) {
          for(let categoryOptionKey in this.categoryOption){
            if(this.categoryOption[categoryOptionKey]['id']==item.value){
              this.categoryOption[categoryOptionKey]['selected']=false;
            }            
          }          
          category_ids.removeAt(i);
          return;
        }
        i++;
      });
    }
  }
  previous(){
    if(this.userDetails.sponsor_type==1){
      this.router.navigate(["personal-details"]);
    }else{
      this.router.navigate(["corporate-details"]);
    }
  }

}
