import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, CanActivate, Router, RouterStateSnapshot, UrlTree } from '@angular/router';
import { Observable } from 'rxjs';
import { AuthService } from './auth.service';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanActivate {
  constructor(private router: Router, private authService: AuthService){ }
  
  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
      let isAuth = this.authService.isLoggedIn();
      let isPresent = false;
      if (!isAuth) {
        this.router.navigate(['/login']);
        return false;
      } else {
        return true;
      }

      // const currentUser = this.authService.currentUserValue;
      // if (currentUser) {
      //   // check if route is restricted by role
      //   if (route.data.roles && route.data.roles.indexOf(currentUser.role) === -1) {
      //       // role not authorised so redirect to home page
      //       this.router.navigate(['/']);
      //       return false;
      //   }

        // authorised so return true
    //     return true;
    // }
      // switch(role) {
      //   case "Role1":
      //       return this._router.createUrlTree(["account-dashboard"]);
      //   case "Role2":
      //       return this._router.createUrlTree(["hr-dashboard"]);
      //   default:
      //       return this._router.createUrlTree(["general-dashboard"]);
      // }

      // if (
      //   !this.authService.isAuthorized(route.data.roleCode)
      // ) {
      //   this.router.navigate(NotFoundPage.PATH);
      //   return false;
      // }
      // return true;
    }
  
}
