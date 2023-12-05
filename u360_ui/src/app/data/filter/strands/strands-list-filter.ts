export class strandsListFilter {
  private search: string;
  private page: string;
  private sort: string;
  private direction: string;
  constructor(
    search: string,
    page: string,
    sort: string,
    direction: string
    ) {
      this.search = search;
      this.page = page;
      this.sort = sort;
      this.direction = direction;
  }
}