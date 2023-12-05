export class userCreateFilter {

    private name: string;
    private email: string;
    private status: string;
    private password: string;
    private user_group_id: string;
  
    constructor(
      name: string,
      email: string,
      status: string,
      password: string,
      user_group_id: string,
     ) {
        this.name = name;
        this.email = email;
        this.status = status;
        this.password = password;
        this.user_group_id = user_group_id;
    }
  }