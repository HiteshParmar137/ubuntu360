export class tutorEditFilter {
  private name: string;
  private email: string;
  private tutor_group: string;
 
  constructor(
    name: string,
    email: string,
    tutor_group: string
    ) {
      this.name = name;
      this.email = email;
      this.tutor_group = tutor_group;
  }
}