export class userEditFilter {

    private id: number;
    private name: string;
    private email: string;
    private status: string;
    private password: string;
    private user_group_id: string;
  
    constructor(
      id:number,
      name: string,
      email: string,
      status: string,
      password: string,
      user_group_id: string,
     ) {
        this.id = id;
        this.name = name;
        this.email = email;
        this.status = status;
        this.password = password;
        this.user_group_id = user_group_id;
    }
  }