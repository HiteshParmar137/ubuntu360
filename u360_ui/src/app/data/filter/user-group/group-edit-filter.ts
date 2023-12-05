export class groupEditFilter {
  private id: string;
  private group_name: string;
  private description: string;
  private status: string;
  private year_group: string;
  private permissions:any;
  constructor(
    id: string,
    group_name: string,
    description: string,
    status: string,
    year_group: string,
    permissions: any,
    ) {
      this.id = id;
      this.group_name = group_name;
      this.description = description;
      this.year_group = year_group;
      this.status = status;
      this.permissions = permissions;
  }
}