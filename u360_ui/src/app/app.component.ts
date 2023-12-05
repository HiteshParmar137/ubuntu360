import { Component, OnInit } from '@angular/core';
import { Router, NavigationEnd , ActivatedRoute } from '@angular/router';
import { IconSetService } from '@coreui/icons-angular';
import { iconSubset } from './icons/icon-subset';
import { Title } from '@angular/platform-browser';
import { filter } from 'rxjs/operators';
@Component({
  selector: 'body',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent implements OnInit {
 
  constructor(
    private router: Router,
    private titleService: Title,
    private iconSetService: IconSetService,
    private activatedRoute: ActivatedRoute,
  ) {
   
    iconSetService.icons = { ...iconSubset };
    
  }

  ngOnInit(): void {
    document.documentElement.style.setProperty('--gray-color', 'gray');
    document.documentElement.style.setProperty('--gray-light-color', '#f9f9f9');
    document.documentElement.style.setProperty('--btn-color', '#fff');
    document.documentElement.style.setProperty('--btn-hover-color', '#2c1b1b');
    this.router.events.pipe(  
      filter(event => event instanceof NavigationEnd),  
    ).subscribe(() => { 
     
      /** title */
      const rt = this.getChild(this.activatedRoute);  
      rt.data.subscribe((data: { title: string; }) => { 
        const titleAppend = 'Ubuntu360 | '+ data.title; 
        this.titleService.setTitle(titleAppend)});  
    });
  }
  
  getChild(activatedRoute: ActivatedRoute):any {  
    if (activatedRoute.firstChild) {  
      return this.getChild(activatedRoute.firstChild);  
    } else {  
      return activatedRoute;  
    }  
  } 
  
}
