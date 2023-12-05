/*
{
  "token": '61b7777ae6432e7c61134dd31e1350d57f880be695c77d8a12b69dadccfaa7ad',
  "password": 'prashant',
  "confirm_password": 'prashant',
}
*/

export class resetPasswordFilter {
  private token: string;
  private password: string;
  private password_confirmation: string;


  constructor(
    token: string,
    password: string,
    password_confirmation : string
    ) {
      this.token = token,
      this.password = password,
      this.password_confirmation = password_confirmation
  }
}