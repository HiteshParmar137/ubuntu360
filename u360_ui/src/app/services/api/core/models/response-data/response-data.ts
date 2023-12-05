export interface ResponseData<T> {
  status_code?: number | string;
  success?: boolean;
  message?: string
  data?: T;
}
  