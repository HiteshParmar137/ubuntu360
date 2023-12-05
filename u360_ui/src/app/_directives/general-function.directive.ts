import * as _ from 'lodash';

export class GeneralFunction {
  static GeneralFunction: string[];
  
  public static setPagination(res: { range: any; total: any; }, currentPage: any, limit: number) {
    // calculate total pages
    let totalPages = 0;
    if (res.range > 0) {
      totalPages = Math.ceil(res.total / limit);
    }
    let current_page = currentPage;

    let startPage: number, endPage: number;
    if (totalPages <= 10) {
      // less than 10 total pages so show all
      startPage = 1;
      endPage = totalPages;
    } else {
      // more than 10 total pages so calculate start and end pages
      if (current_page <= 6) {
        startPage = 1;
        endPage = 10;
      } else if (current_page + 4 >= totalPages) {
        startPage = totalPages - 9;
        endPage = totalPages;
      } else {
        startPage = current_page - 5;
        endPage = current_page + 4;
      }
    }

    // calculate start and end item indexes
    let startIndex = (current_page - 1) * limit;
    let endIndex = Math.min(startIndex + limit - 1, limit - 1);

    // create an array of pages to ng-repeat in the pager control
    let pages = _.range(startPage, endPage + 1);
    // return object with all pager properties required by the view
    var pager = {
      totalItems: res.total,
      currentPage: current_page,
      pageSize: limit,
      totalPages: totalPages,
      startPage: startPage,
      endPage: endPage,
      startIndex: startIndex,
      endIndex: endIndex,
      pages: pages,
    };
    return pager;
  }

  public static setAuthorizeModule(module: string, action: string) {
    localStorage.setItem('module_name', module);
    localStorage.setItem('action', action);
    return true;
  }
  
}
