export class passwordChangeFilter {
  private id: number;
  private current_password: string;
  private new_password: string;
  private confirm_password: string;
  constructor(
    id: number,
    current_password: string,
    new_password: string,
    confirm_password: string
  ) {
    this.id = id;
    this.current_password = current_password;
    this.new_password = new_password;
    this.confirm_password = confirm_password;
  }
}
