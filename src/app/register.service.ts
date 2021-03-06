import { Injectable } from '@angular/core';
import { HttpClient, } from '@angular/common/http';
import { TokenService } from './token.service';
import { BehaviorSubject } from 'rxjs';
import { Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class RegisterService {
  private registerEndpoint = 'api/register.php';
  private addUserEndpoint = 'api/addUser.php';
  private loginEndpoint = 'api/login.php';
  private logoutEndpoint = 'api/logout.php';

  public loggedIn$ = new BehaviorSubject<boolean>(false);

  constructor(
    private http: HttpClient,
    private tokenSrv: TokenService,
    private router: Router
  ) { }
  
  get isLoggedIn() {
    return this.loggedIn$.asObservable();
  }

  login(data: any){
    return this.http.post(this.loginEndpoint, data);
  }

  logout(){
   this.http.post(this.logoutEndpoint, {});
   this.loggedIn$.next(false);
   this.tokenSrv.setToken('');
   this.router.navigate(['login']);
  }


  registerUser(data: any){
    return this.http.post(this.registerEndpoint, data, { observe: 'response'});
  }

  addUser(data: any) {
    return this.http.post(this.addUserEndpoint, data);
  }


}
