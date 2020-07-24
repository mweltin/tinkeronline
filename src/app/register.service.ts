import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { TokenService } from './token.service';
import { BehaviorSubject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class RegisterService {

  private registerEndpoint = 'api/register.php';
  private addUserEndpoint = 'api/addUser.php';
  private loginEndpoint = 'api/login.php';
  private logoutEndpoint = 'api/logout.php';

  public loggedIn$ = new BehaviorSubject<boolean>(false);

  get isLoggedIn() {
    return this.loggedIn$.asObservable();
  }

  login(data: any){
    return this.http.post(this.loginEndpoint, data);
  }

  logout(){
   this.http.post(this.logoutEndpoint, {}, {
      headers:
        { Authorzie: this.tokenSrv.getToken() },
      observe: 'response'
    });
   this.loggedIn$.next(false);
  }

  constructor(
    private http: HttpClient,
    private tokenSrv: TokenService
  ) { }

  registerUser(data: any){
    return this.http.post(this.registerEndpoint, data, { observe: 'response'});
  }

  addUser(data: any) {
    const httpOptions = {
      headers:
      { Authorzie: this.tokenSrv.getToken() },
      observe: 'body'
    };
    return this.http.post(this.addUserEndpoint, data, {
      headers:
        { Authorzie: this.tokenSrv.getToken() },
      observe: 'response'
    });
  }


}
