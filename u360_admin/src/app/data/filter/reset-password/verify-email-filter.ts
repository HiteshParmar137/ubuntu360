/*
{
  "token": 'zjhjhfdshfjsdhfjhjdshfjh',
  'flag" : 'verify_email"
}
*/

export class verifyEmailFilter {

    private token: string;
    private flag: string;
  
    constructor(
      token: string,
      flag: string
    ) {
        this.token = token;
        this.flag = flag;

    }
  }