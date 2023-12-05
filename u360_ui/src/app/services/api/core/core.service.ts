import * as Models from './models/response-data/response-data';
import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export abstract class CoreService {
  
  abstract init(): Promise<void>;
  abstract doGet(method: string, data: string): Promise<Models.ResponseData<any>>;
  abstract doPost(method: string, data: string): Promise<Models.ResponseData<any>>;  
}

