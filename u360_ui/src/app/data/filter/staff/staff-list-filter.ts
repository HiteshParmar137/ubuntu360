export class groupListFilter {
  private page: string;
  private sort: string;
  private direction: string;
  constructor(
    page: string,
    sort: string,
    direction: string
    ) {
      this.page = page;
      this.sort = sort;
      this.direction = direction;
  }
}