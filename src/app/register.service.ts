import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { TokenService } from './token.service';

@Injectable({
  providedIn: 'root'
})
export class RegisterService {

  private registerEndpoint = 'api/register.php';
  private addUserEndpoint = 'api/addUser.php';
  private loginEndpoint = 'api/login.php';

  constructor(
    private http: HttpClient,
    private tokenSrv: TokenService
  ) { }

  registerUser(data: any){
    return this.http.post(this.registerEndpoint, data, { observe: 'response'});
  }
/*
{ headers ?: HttpHeaders | { [header: string]: string | string[]; };
observe ?: "body";
params ?: HttpParams | { [param: string]: string | string[]; };
reportProgress ?: boolean;
responseType: "arraybuffer";
withCredentials ?: boolean;
}
*/

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

  login(data: any){
    return this.http.post(this.loginEndpoint, data);
  }
}
