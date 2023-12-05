import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ProjectFollowedComponent } from './project-followed.component';

describe('ProjectFollowedComponent', () => {
  let component: ProjectFollowedComponent;
  let fixture: ComponentFixture<ProjectFollowedComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ProjectFollowedComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ProjectFollowedComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
