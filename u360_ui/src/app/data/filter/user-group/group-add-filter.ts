export class groupAddFilter {
  private group_name: string;
  private description: string;
  private status: string;
  private year_group: string;
  private permissions:any;
  constructor(
    group_name: string,
    description: string,
    year_group: string,
    status: string,
    permissions: any,
    ) {
      this.group_name = group_name;
      this.description = description;
      this.year_group = year_group;
      this.status = status;
      this.permissions = permissions;
  }
}