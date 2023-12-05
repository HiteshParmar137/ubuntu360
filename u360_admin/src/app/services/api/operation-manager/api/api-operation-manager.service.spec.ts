import { TestBed } from '@angular/core/testing';

import { ApiOperationManagerService } from './api-operation-manager.service';

describe('ApiOperationManagerService', () => {
  let service: ApiOperationManagerService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(ApiOperationManagerService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
