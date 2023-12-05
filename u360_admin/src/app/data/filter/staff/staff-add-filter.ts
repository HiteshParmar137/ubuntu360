export class staffAddFilter {
  private name: string;
  private email: string;
  private password: string;
  private tutor_group: string; 
  private permission_group_id: string; 

  constructor(
    name: string,
    email: string,
    password: string,
    tutor_group: string,
    permission_group_id: string,
    ) {
      this.name = name;
      this.email = email;
      this.password = password;
      this.tutor_group = tutor_group;
      this.permission_group_id = permission_group_id;


  }
}