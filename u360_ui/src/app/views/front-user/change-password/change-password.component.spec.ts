import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SecuityComponent } from './security.component';

describe('SecuityComponent', () => {
  let component: SecuityComponent;
  let fixture: ComponentFixture<SecuityComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [SecuityComponent],
    }).compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(SecuityComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
