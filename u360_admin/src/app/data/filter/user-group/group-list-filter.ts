export class groupListFilter {
  private page: string;
  private sort: string;
  private direction: string;
  private filter_name: string;
  private status: string;
  constructor(
    page: string,
    sort: string,
    direction: string,
    filter_name: string,
    status: string
    ) {
      this.page = page;
      this.sort = sort;
      this.direction = direction;
      this.filter_name = filter_name;
      this.status = status;
  }
}