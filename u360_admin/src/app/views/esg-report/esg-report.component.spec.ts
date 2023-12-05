import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EsgReportComponent } from './esg-report.component';

describe('EsgReportComponent', () => {
  let component: EsgReportComponent;
  let fixture: ComponentFixture<EsgReportComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ EsgReportComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(EsgReportComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
