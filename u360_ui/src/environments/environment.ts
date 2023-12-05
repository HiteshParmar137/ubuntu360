// This file can be replaced during build by using the `fileReplacements` array.
// `ng build` replaces `environment.ts` with `environment.prod.ts`.
// The list of file replacements can be found in `angular.json`.

export const environment = {
  production: false,
  //live
  //API_URL: 'http://ec2-3-142-183-191.us-east-2.compute.amazonaws.com/api',
  //API_URL: 'https://ubuntu360.es/api',
  //staging
  //API_URL: 'https://ec2-65-0-26-172.ap-south-1.compute.amazonaws.com/api',
  API_URL: 'http://localhost/u360_api/api',
  google_client_Id: '1091253019673-13ep9n2sb3vi2g02e8mh6a4fpm15co7v.apps.googleusercontent.com',
  fb_client_Id:'855053545945862',
};

/*
 * For easier debugging in development mode, you can import the following file
 * to ignore zone related error stack frames such as `zone.run`, `zoneDelegate.invokeTask`.
 *
 * This import should be commented out in production mode because it will have a negative impact
 * on performance if an error is thrown.
 */