import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EsgReportsComponent } from './esg-reports.component';

describe('EsgReportsComponent', () => {
  let component: EsgReportsComponent;
  let fixture: ComponentFixture<EsgReportsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ EsgReportsComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(EsgReportsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
