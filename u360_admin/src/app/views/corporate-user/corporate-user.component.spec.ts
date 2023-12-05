import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CorporateUserComponent } from './corporate-user.component';

describe('CorporateUserComponent', () => {
  let component: CorporateUserComponent;
  let fixture: ComponentFixture<CorporateUserComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ CorporateUserComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CorporateUserComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
