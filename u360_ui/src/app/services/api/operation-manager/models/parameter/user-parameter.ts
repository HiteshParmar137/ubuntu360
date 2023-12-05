import { Parameter } from '../parameter/parameter';

export interface UserParameter extends Parameter<any> {
  id?:number,
  name?: number;
  email?: string;
  status?: string;
}
